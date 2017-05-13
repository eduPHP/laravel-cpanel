<?php


use GuzzleHttp\Handler\MockHandler;

trait MockEmailResponses
{
    /**
     * @return MockHandler
     */
    private function mockSuccessfullFetch()
    {
        return $this->mockResponse('email.fetch.emails.index');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullEmptyFetch()
    {
        return $this->mockResponse('email.fetch.empty');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullFilteredFetch()
    {
        return $this->mockResponse('email.fetch.emails.filter');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullForwardFetch()
    {
        return $this->mockResponse('email.fetch.forward.index');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullStoreForwardResponse()
    {
        return $this->mockResponse('email.fetch.forward.store-success');
    }

    /**
     * @return MockHandler
     */
    private function mockErrorStoreForwardResponse()
    {
        return $this->mockResponse('email.fetch.forward.store-error');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullDestroyForwardResponse()
    {
        return $this->mockResponse('email.fetch.forward.destroy-success');
    }

    /**
     * @return MockHandler
     */
    private function mockErrorDestroyForwardResponse()
    {
        return $this->mockResponse('email.fetch.forward.destroy-error');
    }

    /**
     * @return MockHandler
     */
    private function mockErrorWeakPasswordResponse()
    {
        return $this->mockResponse('email.fetch.emails.password-weak');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessChangingPasswordResponse()
    {
        return $this->mockResponse('email.fetch.emails.password-success');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullQuotaResponse()
    {
        return $this->mockResponse('email.fetch.emails.quota-success');
    }

    /**
     * @return MockHandler
     */
    private function mockErrorUnauthorizedEmailResponse()
    {
        return $this->mockResponse('email.fetch.emails.permission-error');
    }

    /**
     * @return MockHandler
     */
    private function mockSuccessfullEmailDestroyResponse()
    {
        return $this->mockResponse('email.fetch.emails.destroy-success');
    }

    /**
     * @return MockHandler
     */
    private function mockStoreErrorResponses()
    {
        return $this->mockResponse([
            'email.fetch.empty', //1 - search for the email but returns nothing
            'email.result-error', //2 - try to store but get an error
        ]);
    }

    /**
     * @return MockHandler
     */
    private function mockStoreSuccessResponses()
    {
        return $this->mockResponse([
            'email.fetch.empty',    //1 - search for the email but returns nothing
            'email.result-success', //2 - try to store and is successfull
            'email.fetch.emails.filter',   //2 - search for the email and returns the new register
        ]);
    }
}
