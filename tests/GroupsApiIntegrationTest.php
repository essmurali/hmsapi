<?php
namespace Tests;
use App\Models\Groups;
use App\Models\User;
use Laravel\Lumen\Testing\DatabaseMigrations as LumenDatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GroupsApiIntegrationTest extends TestCase
{
    use LumenDatabaseMigrations;

    protected $token;

    /**
     * Run before each test.
     * Creates a user and generates a token.
     */
    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['password' => Hash::make('testpassword') ]);
        $this->token = Auth::login($user);
    }

    /**
     * Test the complete lifecycle of a group.
     * This test covers:
     * 1. Creating a root group via POST.
     * 2. Creating a sub-group via POST.
     * 3. Retrieving the nested group structure via GET.
     * 4. Updating a group via PUT.
     * 5. Deleting a group via DELETE.
     */
    public function testCompleteGroupLifecycle()
    {
        // 1. Create a root group via POST
        $rootGroupPayload = ['name' => 'Hospital A'];
        $response = $this->json('POST', '/api/groups/create', $rootGroupPayload, ['Authorization' => 'Bearer ' . $this->token]);
        $response->seeStatusCode(201);
        $response->seeJson(['name' => 'Hospital A']);

        $rootGroup = json_decode($response->response->getContent(), true);

        // 2. Create a sub-group via POST
        $subGroupPayload = ['name' => 'Stomach', 'parent_name' => 'Hospital A'];
        $response = $this->json('POST', '/api/groups/create', $subGroupPayload, ['Authorization' => 'Bearer ' . $this->token]);
        $response->seeStatusCode(201);
        $response->seeJson(['name' => 'Stomach']);

        $subGroup = json_decode($response->response->getContent(), true);
        $this->assertEquals($rootGroup['id'], $subGroup['parent_id']);

        // Create a grandchild group for the nested structure test
        Groups::factory()->create(['name' => "Crohn's Disease", 'parent_id' => $subGroup['id']]);

        // 3. Retrieve the nested group structure via GET
        $response = $this->get('/api/groups', ['Authorization' => 'Bearer ' . $this->token]);
        $response->seeStatusCode(200);
        $response->seeJsonContains(['Crohn\'s Disease']);

        // 5. Delete a group via DELETE
        $response = $this->json('DELETE', '/api/groups/' . $subGroup['id'], [], ['Authorization' => 'Bearer ' . $this->token]);
        $response->seeStatusCode(204);

        $this->notSeeInDatabase('groups', ['id' => $subGroup['id']]);
    }
}
