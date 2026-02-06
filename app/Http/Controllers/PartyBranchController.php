<?php

namespace App\Http\Controllers;

use App\Models\Party;
use App\Models\PartyBranch;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PartyBranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Reuse party permissions
        $this->middleware('permission:core.party.update')->only(['store']);
        $this->middleware('permission:core.party.delete')->only(['destroy']);
    }

    /**
     * Lightweight JSON endpoint for fetching a party's branches (GSTINs).
     * Used by purchase flows to determine GST split based on selected branch.
     */
    public function apiIndex(Request $request, Party $party): JsonResponse
    {
        $branches = $party->branches()
            ->orderByDesc('is_primary')
            ->orderBy('branch_name')
            ->get([
                'id',
                'party_id',
                'branch_name',
                'gstin',
                'gst_state_code',
                'is_primary',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'pincode',
            ]);

        return response()->json([
            'party_id'  => $party->id,
            'branches'  => $branches,
        ]);
    }

    /**
     * Store a newly created branch for the given party.
     *
     * This is mainly used to store additional GSTIN registrations for the same legal entity
     * (e.g. branches in other states).
     */
    public function store(Request $request, Party $party)
    {
        $this->authorizeForUser($request->user(), 'update', $party);

        $data = $request->validate([
            'branch_name'   => ['nullable', 'string', 'max:150'],
            'gstin'         => ['required', 'string', 'max:20', function ($attribute, $value, $fail) use ($party) {
                $gstin = strtoupper(preg_replace('/\s+/', '', (string) $value));

                // Don't allow duplicating the same GSTIN already saved as the party primary GSTIN
                if ($party->gstin && $gstin === $party->gstin) {
                    $fail('This GSTIN is already saved as the party primary GSTIN.');
                    return;
                }

                // Guard: GSTIN must belong to the same PAN as the party (same legal entity)
                if ($party->pan && strlen($gstin) >= 12) {
                    $maybePan = substr($gstin, 2, 10);
                    if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $maybePan) && $maybePan !== $party->pan) {
                        $fail('This GSTIN belongs to a different PAN. Please create/open the correct party instead of adding it as a branch.');
                        return;
                    }
                }

                // Check other party primary GSTINs
                $existsInParties = Party::query()
                    ->where('id', '!=', $party->id)
                    ->whereNotNull('gstin')
                    ->where('gstin', $gstin)
                    ->exists();

                // Check branches (any party, including same party)
                $existsInBranches = PartyBranch::query()
                    ->whereNotNull('gstin')
                    ->where('gstin', $gstin)
                    ->exists();

                if ($existsInParties || $existsInBranches) {
                    $fail('This GSTIN is already present in the system. Please use the existing party/branch.');
                }
            }],

            'address_line1' => ['nullable', 'string', 'max:200'],
            'address_line2' => ['nullable', 'string', 'max:200'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'pincode'       => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
        ]);

        $data['party_id'] = $party->id;

        // Normalize GSTIN
        $data['gstin'] = strtoupper(preg_replace('/\s+/', '', (string) $data['gstin']));

        $party->branches()->create($data);

        // If party PAN is blank, try to populate from GSTIN (positions 3-12)
        if (!$party->pan && strlen($data['gstin']) >= 12) {
            $maybePan = substr($data['gstin'], 2, 10);
            if (preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $maybePan)) {
                $party->update(['pan' => $maybePan]);
            }
        }

        return redirect()
            ->route('parties.show', $party)
            ->with('success', 'Branch GSTIN added successfully.');
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Request $request, PartyBranch $branch)
    {
        $party = $branch->party;

        $this->authorizeForUser($request->user(), 'update', $party);

        $branch->delete();

        return redirect()
            ->route('parties.show', $party)
            ->with('success', 'Branch deleted successfully.');
    }
}
