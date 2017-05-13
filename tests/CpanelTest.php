<?php


class CpanelTest extends BaseCpanel
{
    /** @test */
    public function it_adds_values_to_the_fields_array()
    {
        //given
        $merge_this = [
            'foo' => 'bar',
        ];

        //when
        $this->cpanel->mergeFields($merge_this);

        //then
        $this->assertArrayHasKey('foo', $this->cpanel->fields);
    }

    /**
     * @test
     * @expectedException        Exception
     * @expectedExceptionMessage Domain name required
     */
    public function it_sqwalks_when_calling_zoneedit_whithout_informing_a_domain_name()
    {
        //when
        $this->cpanel->zoneEdit();
        //then sqwalks
    }
}
