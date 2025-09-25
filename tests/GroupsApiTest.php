<?php

namespace Tests;
use App\Models\Groups;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations as LumenDatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class GroupsApiTest extends TestCase
{
    use LumenDatabaseMigrations; // Rolls back migrations after each test

    protected $token;

    /**
     * Run before each test.
     * Creates a user and generates a token.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Create a test user
        $user = User::factory()->create();

        // Generate a token for the user
        $this->token = Auth::login($user);
    }

    /**
     * Test GET operation to list groups.
     * Requires authentication.
     */
    public function testGetGroups()
    {
        // 1. Create a root category
        $root = Groups::factory()->create(['name' => 'Hospital A']);

        // 2. Create a child category
        $child = Groups::factory()->create([
            'name' => 'Stomach',
            'parent_id' => $root->id
        ]);

        // 3. Create grandchild categories (leaf nodes)
        Groups::factory()->create([
            'name' => "Crohn's Disease",
            'parent_id' => $child->id
        ]);
        Groups::factory()->create([
            'name' => 'Ulcerative Colitis',
            'parent_id' => $child->id
        ]);

        $this->get('/api/groups', ['Authorization' => 'Bearer ' . $this->token])
            ->seeStatusCode(200)
            ->seeJsonContains([
                "Crohn's Disease"
            ])
            ->seeJsonContains([
                "Ulcerative Colitis"
            ]);
    }

    /**
     * Test CREATE operation for a new root category.
     *
     * @return void
     */
    public function testCreateParentGroup()
    {
        $payload = ['name' => 'New Root Group'];

        $this->json('POST', '/api/groups/create', $payload, ['Authorization' => 'Bearer ' . $this->token])
            ->seeStatusCode(201)
            ->seeJson(['name' => 'New Root Group']);

        $this->seeInDatabase('groups', [
            'name' => 'New Root Group',
            'parent_id' => null,
        ]);
    }

    /**
     * Test CREATE operation for a new sub-group.
     *
     * @return void
     */
    public function testCreateSubGroup()
    {
        // Create a parent category first
        $parent = Groups::factory()->create(['name' => 'Parent Group']);

        $payload = [
            'name' => 'New Sub Group',
            'parent_name' => 'Parent Group',
        ];

        $this->json('POST', '/api/groups/create', $payload, ['Authorization' => 'Bearer ' . $this->token])
            ->seeStatusCode(201)
            ->seeJson(['name' => 'New Sub Group']);

        $this->seeInDatabase('groups', [
            'name' => 'New Sub Group',
            'parent_id' => $parent->id,
        ]);
    }


    /**
     * Test DELETE operation for a group.
     *
     * @return void
     */
    public function testDeleteGroup()
    {
        // Create a category to delete
        $category = Groups::factory()->create(['name' => 'Category to Delete']);

        $this->json('DELETE', '/api/groups/' . $category->id, [], ['Authorization' => 'Bearer ' . $this->token])
            ->seeStatusCode(204); // No content on successful deletion

        $this->notSeeInDatabase('groups', ['id' => $category->id]);
    }

    /**
     * Test that updating a non-existent group returns a 404.
     *
     * @return void
     */
    public function testUpdateNonExistentGroup()
    {
        $payload = ['name' => 'Non Existent Category'];
        $this->json('PUT', '/api/groups/999', $payload, ['Authorization' => 'Bearer ' . $this->token])
            ->seeStatusCode(404)
            ->seeJson(['message' => 'Group not found']);
    }
}
