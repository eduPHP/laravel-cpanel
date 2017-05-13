<?php

use GuzzleHttp\Handler\MockHandler;

trait MockZoneEditResponses
{
    /**
     * @return MockHandler
     */
    private function mockZoneEditSuccessfullFetch()
    {
        return $this->mockResponse('zoneedit.fetch.index');
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditSuccessfullFetchFilter()
    {
        return $this->mockResponse('zoneedit.fetch.filter');
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditSuccessfullFetchEmpty()
    {
        return $this->mockResponse('zoneedit.fetch.empty');
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditStoreResponses()
    {
        return $this->mockResponse(['zoneedit.fetch.empty', 'zoneedit.result.success', 'zoneedit.fetch.filter']);
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditStoreErrorResponse()
    {
        return $this->mockResponse(['zoneedit.fetch.empty', 'result-error']);
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditDestroyErrorResponse()
    {
        return $this->mockResponse(['zoneedit.fetch.filter', 'result-error']);
    }

        /**
         * @return MockHandler
         */
        private function mockZoneEditDestroySuccessResponse()
        {
            return $this->mockResponse(['zoneedit.fetch.filter', 'zoneedit.result.success']);
        }

    /**
     * @return MockHandler
     */
    private function mockZoneEditUpdateErrorResponse()
    {
        return $this->mockResponse(['zoneedit.fetch.filter', 'zoneedit.fetch.filter', 'result-error']);
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditUpdateAlreadyExistsErrorResponse()
    {
        return $this->mockResponse(['zoneedit.fetch.filter', 'zoneedit.fetch.filter2']);
    }

    /**
     * @return MockHandler
     */
    private function mockZoneEditPermissionDenied()
    {
        return $this->mockResponse('zoneedit.fetch.domain-error');
    }
}
