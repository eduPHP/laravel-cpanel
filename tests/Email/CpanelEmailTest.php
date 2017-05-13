<?php

use Illuminate\Support\Collection;
use Swalker2\Cpanel\Email\CpanelEmail;
use Swalker2\Cpanel\Email\Email;

class CpanelEmailTest extends BaseCpanel
{
    use MockEmailResponses;

    /** @test */
    public function it_verifies_if_the_email_module_is_loading_correctly()
    {
        //given
        $email_mod = $this->cpanel->email();
        //then
        $this->assertInstanceOf(CpanelEmail::class, $email_mod);
        $this->assertArrayHasKey('cpanel_jsonapi_module', $this->cpanel->fields);
        $this->assertEquals('Email', $this->cpanel->fields['cpanel_jsonapi_module']);
    }

    /** @test */
    public function it_returns_an_emails_collection()
    {
        //given
        $email_module = $this->cpanel->email()->testHandler(
            $this->mockSuccessfullFetch()
        );
        //when
        $emails = $email_module->fetch();
        //then
        $this->assertInstanceOf(Collection::class, $emails);
        $this->assertCount(3, $emails);
    }

    /** @test */
    public function it_returns_an_emails_collection_with_an_email_when_filtering_for_the_given_string()
    {
        //given
        $email_module = $this->cpanel->email()->testHandler(
            $this->mockSuccessfullFilteredFetch()
        )->filter('foo@yourdomain.com');
        //when
        $emails = $email_module->fetch();
        $first_item = $emails->first();
        //then
        $this->assertInstanceOf(Collection::class, $emails);
        $this->assertCount(1, $emails);
        $this->assertEquals('foo', $first_item->user);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage The e-mail "foo@yourdomain.com" already exists.
     */
    public function it_sqwalks_when_trying_to_add_an_email_that_already_exists()
    {
        //given
        $email_module = $this->cpanel->email();
        $new_email = new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]);
        $email_module->testHandler(
            $this->mockSuccessfullFilteredFetch()
        );
        //when
        $email_module->store($new_email, 'dummyPw123');

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Sorry, you do not have access to the domain 'yourdomain.com'
     */
    public function it_sqwalks_when_trying_to_add_an_email_and_the_server_returns_an_error()
    {
        //given
        $email_module = $this->cpanel->email();
        $new_email = new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]);
        $email_module->testHandler(
            $this->mockStoreErrorResponses()
        );
        //when
        $email_module->store($new_email, 'dummyPw123');

        //then it sqwalks
    }

    /** @test */
    public function it_returns_an_emails_collection_with_the_newly_stored_email()
    {
        //given
        $email_module = $this->cpanel->email();
        $new_email = new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]);
        $email_module->testHandler(
            $this->mockStoreSuccessResponses()
        );
        //when
        $response = $email_module->store($new_email, 'dummyPw123');
        $email = $response->first();

        //then
        $this->assertInstanceOf(Collection::class, $response);
        $this->assertCount(1, $response);
        $this->assertEquals('foo@yourdomain.com', $email->email);
    }

    /** @test */
    public function it_searches_for_an_email_forward_for_the_given_email_object()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessfullForwardFetch()
        );

        //when
        $fetch_fw = $email_module->fetchForward('foo', 'yourdomain.com');
        $forward = $fetch_fw->first();

        //then
        $this->assertCount(1, $fetch_fw);
        $this->assertEquals('foo@yourdomain.com', $forward->dest);
    }

    /** @test */
    public function it_successfully_stores_a_forward_address_for_the_given_email()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessfullStoreForwardResponse()
        );

        //when
        $stored_forward = $email_module->storeForward(new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]), 'foobar@gmail.com');

        //then
        $this->assertCount(1, $stored_forward);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage You don't have permissions to execute this action.
     */
    public function it_sqwalks_when_storing_a_forward_address_for_the_given_email_returns_an_error()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockErrorStoreForwardResponse()
        );

        //when
        $stored_forward = $email_module->storeForward(new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]), 'foobar@gmail.com');

        //then
        $this->assertCount(1, $stored_forward);
    }

    /** @test */
    public function it_removes_an_forward_address_from_the_given_email_address_and_target()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessfullDestroyForwardResponse()
        );

        //when
        $stored_forward = $email_module->destroyForward(new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]), 'foobar@gmail.com');

        //then
        $this->assertTrue($stored_forward);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Failed to delete forwarder.
     */
    public function it_sqwalks_while_trying_to_remove_an_forward_address_from_the_given_email_address_and_target_and_it_returns_an_error()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockErrorDestroyForwardResponse()
        );

        //when
        $stored_forward = $email_module->destroyForward(new Email([
            'user'   => 'foo',
            'domain' => 'yourdomain.com',
        ]), 'foobar@gmail.com');

        //then
        $this->assertTrue($stored_forward);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Sorry, the password you selected cannot be used because it is too weak and would be too easy to guess.  Please select a password with strength rating of 40  or higher
     */
    public function it_sqwalks_when_trying_to_update_the_password_given_an_weak_password()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockErrorWeakPasswordResponse()
        );

        //when
        $changed = $email_module->updatePassword(
            new Email(['user'=>'foo', 'yourdomain.com']),
            'best_passwd_evah' //weak password
        );

        //then it sqwalks
    }

    /** @test */
    public function it_password()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessChangingPasswordResponse()
        );
        $pass_is_crazy = base64_encode('ohhh');

        //when
        $changed = $email_module->updatePassword(
            new Email(['user' => 'foo', 'yourdomain.com']),
            $pass_is_crazy
        );

        //then
        $this->assertTrue($changed);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage Invalid Email Object, must fill the domain, user and _diskquota properties.
     */
    public function it_sqwalks_when_you_inform_an_invalid_object()
    {
        //given
        $email_module = $this->cpanel->email();
        $bad_email = new Email();

        //when
        $email_module->updateQuota($bad_email);

        //then it sqwalks
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage You do not own this email account.
     */
    public function it_sqwalks_when_you_try_to_change_the_quota_of_an_unauthorized_email()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockErrorUnauthorizedEmailResponse()
        );
        $email = new Email([
            'domain'     => 'yolo.com',
            'user'       => 'de-buenas',
            '_diskquota' => 999, // megabytes converte automatico
        ]);

        //when
        $email_module->updateQuota($email);

        //then it sqwalks
    }

    /** @test */
    public function it_successfully_changes_the_email_quota()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessfullQuotaResponse()
        );
        $email = new Email([
            'domain'      => 'yourdomain.com',
            'user'        => 'foo',
            '_diskquota'  => 150, // megabytes converte automatico
        ]);

        //when
        $updated = $email_module->updateQuota($email);

        //then
        $this->assertTrue($updated);
    }

    /** @test
     * @expectedException        Exception
     * @expectedExceptionMessage You do not own this email account.
     */
    public function it_sqwalks_when_you_try_to_remove_someones_else_email()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockErrorUnauthorizedEmailResponse()
        );
        $email = new Email([
            'domain'     => 'yolo.com',
            'user'       => 'foo',
        ]);

        //when
        $removed = $email_module->destroy($email);

        //then
        $this->assertTrue($removed);
    }

    /** @test */
    public function it_destroys_the_given_email_address()
    {
        //given
        $email_module = $this->cpanel->email();
        $email_module->testHandler(
            $this->mockSuccessfullEmailDestroyResponse()
        );
        $email = new Email([
            'domain' => 'yourdomain.com',
            'user'   => 'foo',
        ]);

        //when
        $removed = $email_module->destroy($email);

        //then
        $this->assertTrue($removed);
    }
}
