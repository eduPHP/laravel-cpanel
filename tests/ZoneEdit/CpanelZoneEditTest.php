<?php


use Illuminate\Support\Collection;
use Swalker2\Cpanel\ZoneEdit\Zone;

class CpanelZoneEditTest extends BaseCpanel
{
    use MockZoneEditResponses;

    /** @test */
    public function it_tests_if_zoneedit_is_initializing_correctly()
    {
        //when
        $this->cpanel->zoneEdit('domain.com');
        //then
        $this->assertEquals($this->cpanel->fields['cpanel_jsonapi_module'], 'ZoneEdit');
    }

    /** @test */
    public function it_fetches_all_a_domain_zones_in_a_given_domain()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditSuccessfullFetch()
        );
        //when
        $zones = $zonedit->fetch();
        //then

        $this->assertInstanceOf(Collection::class, $zones);
        $this->assertCount(3, $zones);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage You don't have permissions to read data from this domain
     */
    public function it_sqwalks_when_you_cant_read_information_from_the_given_domain()
    {
        //given

        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditPermissionDenied()
        );
        //when
        $zonedit->fetch();
        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage The Zone foo.yourdomain.com already exists
     */
    public function it_sqwalks_when_you_try_to_store_a_zone_and_there_is_already_a_zone_with_the_given_name()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditSuccessfullFetch() // there is already a foo.yourdomain.com
        );
        $newzone = new Zone([
            'name'    => 'foo',
            'domain'  => 'yourdomain.com',
            'address' => '10.10.10.10',
        ]);

        //when
        $zonedit->store($newzone);

        //then it sqwalks
    }

    /** @test */
    public function it_searches_for_a_dns_zone_with_the_given_name_in_the_given_domain_zone()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditSuccessfullFetchFilter()
        );
        //when
        $results = $zonedit->filter('foobar')->fetch();
        //then

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(1, $results);
    }

    /** @test */
    public function it_returns_an_empty_collection_if_no_zones_matches_the_filter()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditSuccessfullFetchEmpty()
        );
        //when
        $results = $zonedit->filter('foobar')->fetch();
        //then

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(0, $results);
    }

    /** @test */
    public function it_returns_a_collection_with_the_created_object_if_the_creation_was_successfull()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditStoreResponses()
        );
        $newzone = new Zone([
            'name'   => 'foo',
            'domain' => 'yourdomain.com',
        ]);

        //when
        $created = $zonedit->store($newzone);

        //then
        $this->assertInstanceOf(Collection::class, $created);

        $createdZone = $created->first();

        $this->assertEquals($createdZone->name, 'foo');
        $this->assertEquals($createdZone->domain, 'yourdomain.com');
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Error trying to insert new Zone
     */
    public function it_sqwalks_when_storing_a_new_zone_and_some_crazy_error_happens()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditStoreErrorResponse()
        );
        $newzone = new Zone([
            'name'    => 'foo',
            'domain'  => 'yourdomain.com',
            'address' => '10.10.10.10',
        ]);

        //when
        $zonedit->store($newzone);

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid object, the "line" must be a valid line number.
     */
    public function it_sqwalks_when_updating_a_zone_and_there_is_no_line_property()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditSuccessfullFetchFilter()
        );
        $zone = $zonedit->filter('foo')->fetch()->first();

        //when we mess it up and try to save
        $zone->line = null;
        $zone->update(['name' => 'todebuenas']);

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Error trying to update DNS Zone.
     */
    public function it_sqwalks_when_updating_a_zone_and_it_returns_an_error()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
        // this response finds the proper zone, but returns and error when saving
            $this->mockZoneEditUpdateErrorResponse()
        );
        $zone = $zonedit
            ->filter('foo')
            ->fetch()
            ->first();

        //when we change it up and try to save
        $zone->update(['name' => 'to-de-buenas']);

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Can't update Zone, the Zone bar.yourdomain.com already exists.
     */
    public function it_sqwalks_when_updating_a_zone_and_it_the_new_name_already_exists_with_other_line()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
        // this response finds the proper zone, but returns and error when saving
            $this->mockZoneEditUpdateAlreadyExistsErrorResponse()
        );
        $zone = $zonedit
            ->filter('foo')
            ->fetch()
            ->first();

        //when we change it up and try to save
        $zone->update(['name' => 'bar']);

        //then it sqwalks
    }

    /** @test */
    public function it_returns_the_updated_object_if_the_process_was_successfull()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
            $this->mockZoneEditStoreResponses()
        );
        $newzone = new Zone([
            'name'   => 'foo',
            'domain' => 'yourdomain.com',
        ]);

        //when
        $created = $zonedit->store($newzone);

        //then
        $this->assertInstanceOf(Collection::class, $created);

        $createdZone = $created->first();

        $this->assertEquals($createdZone->name, 'foo');
        $this->assertEquals($createdZone->domain, 'yourdomain.com');
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid Object, the "line" property is neccessary while removing a Zone.
     */
    public function it_sqwalks_when_there_is_no_line_property_while_trying_to_destroy_zone()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
        // this response finds the proper zone, but returns and error when saving
            $this->mockZoneEditSuccessfullFetchFilter()
        );
        $zone = $zonedit
            ->filter('foo')
            ->fetch()
            ->first();

        // we mess it up and try to destroy
        $zone->line = null;
        $zone->destroy();

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Error trying to remove Zone.
     */
    public function it_sqwalks_when_there_is_an_error_response_while_trying_to_remove()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
        // this response finds the proper zone, but returns and error when saving
            $this->mockZoneEditDestroyErrorResponse()
        );
        $zone = $zonedit
            ->filter('foo')
            ->fetch()
            ->first();
        //when
        $zone->destroy();

        //then it sqwalks
    }

    /** @test */
    public function it_returns_true_when_sucessfully_removing()
    {
        //given
        $zonedit = $this->cpanel->zoneEdit('yourdomain.com');
        $zonedit->testHandler(
        // this response finds the proper zone, but returns and error when saving
            $this->mockZoneEditDestroySuccessResponse()
        );
        $zone = $zonedit
            ->filter('foo')
            ->fetch()
            ->first();
        //when
        $deleting = $zone->destroy();

        //then

        $this->assertTrue($deleting);
    }
}
