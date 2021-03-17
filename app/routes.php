<?php

$app->group('/sentool/paiementmarchand', function () {
	$this->post('/wizall', App\Controllers\api_sentoolController::class .':wizall');
	$this->post('/cashinFreeMoney', App\Controllers\api_sentoolController::class .':cashinFreeMoney');	
	$this->post('/PMFreeMoney', App\Controllers\api_sentoolController::class .':PMFreeMoney');	
});








