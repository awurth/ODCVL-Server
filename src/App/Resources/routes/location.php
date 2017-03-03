<?php

$app->group('/locations', function () {
    $this->get('', 'LocationController:getCollection')->setName('get_locations');
    $this->get('/{id:[0-9]+}', 'LocationController:get')->setName('get_location');
});
