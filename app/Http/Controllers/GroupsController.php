<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\Groups;
use \Illuminate\Contracts\Validation\Validator;
use App\Traits\FormatsGroups;


class GroupsController extends Controller
{
    use FormatsGroups;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $groups = Groups::with('children')
            ->whereNull('parent_id')
            ->get();
        $formattedGroups = $this->formatGroups($groups);
        return response()->json(['status' => 'success','result' => $formattedGroups]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:groups',
            'parent_name' => 'nullable|string|max:255',
        ]);
        $parentCategory = null;
        if ($request->filled('parent_name')) {
            $parentCategory = Groups::where('name', $request->input('parent_name'))->first();

            // Handle the case where the parent is not found
            if (!$parentCategory) {
                return response()->json(['error' => 'Parent group name not found.'], 404);
            }
        }
        $groups = Groups::create([
            'name' => $request->input('name'),
            'parent_id' => $parentCategory ? $parentCategory->id : null,
        ]);
        return response()->json($groups, 201);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the group by its ID
        $group = Groups::find($id);

        // Check if the group exists
        if (!$group) {
            return response()->json(['message' => 'Group not found'], 404);
        }

        // Validate the request data
        $this->validate($request, [
            'name' => 'required|unique:groups',
            'parent_name' => 'nullable|string|max:255',
        ]);
        $parentCategory = null;
        if ($request->filled('parent_name')) {
            $parentCategory = Groups::where('name', $request->input('parent_name'))->first();

            // Handle the case where the parent is not found
            if (!$parentCategory) {
                return response()->json(['error' => 'Parent group name not found.'], 404);
            }
        }

        // Update the group's attributes with the request data
        $group->name = $request->input('name');
        $group->parent_id = $parentCategory->id;
        // Update other attributes as needed

        // Save the changes to the database
        $group->save();

        // Return a JSON response with the updated group
        return response()->json($group, 200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $groups = Groups::find($id);
        if (!$groups) {
            return response()->json(['message' => 'Group not found'], 404);
        }
        $groups->delete();
        return response()->json(['message' => 'Group deleted successfully'], 204);

    }
}
