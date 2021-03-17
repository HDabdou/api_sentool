<?php

namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use \App\Controller;



class api_sentoolController extends Controller {
  private $bdd;
  public function __construct(){
    $this->bdd=new \pdo("mysql:host=localhost;dbname=api_sentool;charset=utf8","root","");
  }

 public function wizall(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    //return "test";
    if(isset($_POST['commande']) && isset($_POST['dateh']) &&isset($_POST['hmac']) &&isset($_POST['algo']) &&isset($_POST['identifiant']) &&isset($_POST['ref_commande']) &&isset($_POST['site']) &&isset($_POST['total']) &&isset($_POST['numero']) &&isset($_POST['service']) ){

        $commande = $request->getParsedBody()['commande'];
        $dateh = $request->getParsedBody()['dateh'];
        $hmac = $request->getParsedBody()['hmac'];
        $algo = $request->getParsedBody()['algo'];
        $identifiant = $request->getParsedBody()['identifiant'];
        $ref_commande = $request->getParsedBody()['ref_commande'];
        $site = $request->getParsedBody()['site'];
        $total = $request->getParsedBody()['total'];
        $numero = $request->getParsedBody()['numero'];
        $service = $request->getParsedBody()['service'];

        try{
           $req=$this->bdd->prepare("SELECT `cle_secrete` FROM `users` WHERE md5(`identifiant`) = :identifiant AND md5(`site`) = :site LIMIT 1");
            $status=$req->execute(array(
              ":identifiant" => $identifiant,
              ":site" => $site,
            ));
            $cle_secrete =$req->fetch();


          if($cle_secrete){
            $cle_bin = pack("H*", $cle_secrete['cle_secrete']);

            $message = "S2M_COMMANDE=$commande"."&S2M_DATEH=$dateh"."&S2M_HTYPE=$algo"."&S2M_IDENTIFIANT=$identifiant"."&S2M_REF_COMMANDE=$ref_commande"."&S2M_SITE=$site"."&S2M_TOTAL=$total";
            $myhmac = strtoupper(hash_hmac(strtolower($algo),$message, $cle_bin));
            //return $response->withJson(array("myhmac"=>$myhmac,"hmac" =>$hmac),200);
            if(strcmp($hmac, $myhmac) == 0){

              $req=$this->bdd->prepare("INSERT INTO `operations`(`id`, `identifiant`, `ref_commande`, `commande`, `date_commande`, `numero`, `montant`, `service`, `etat`) VALUES (null, :identifiant, :ref_commande, :commande, :date_commande, :numero, :montant, :service,0)");
              $st=$req->execute(array(
              
               ":identifiant" =>$identifiant,
               ":ref_commande" =>$ref_commande,
               ":commande" =>$commande,
               ":date_commande" =>$dateh,
               ":numero" =>$numero,
               ":montant" =>$total,
               ":service" =>$service,
              ));       
              $lastInserted = $this->bdd->lastInsertId();
              if($st){
                //$nextOne  =   file_get_contents("files/nextOne.txt");
                /*$file = fopen("files/request/".$lastInserted.".txt", 'w')or die("Unable to open file!");
                fwrite($file, $numero."###".$service."###".$total."###".$ref_commande."###".$commande) ;
                fclose($file);
                
                return $response->withJson(array("status"=>$st,"message" =>"opération encours","idTransaction"=>$lastInserted),200); */
                 $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => "https://465dc7810810.ngrok.io/webServicePhp/pipiper.Wizall.php",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS =>"external_id=".$lastInserted."&numero=".$numero."&montant=".$total,
                      CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/x-www-form-urlencoded"
                      ),
                    ));

                    $response = curl_exec($curl);

                    //echo $response;
                    $err = curl_error($curl);
                    curl_close($curl);

                    if ($err) {
                      return "cURL Error #:" . $err;
                    } else {
                      return $response;
                    }

              }else{
                return $response->withJson(array("status"=>$st,"message" =>"opération echec"),200); 
              }

            }else{
              return $response->withJson(array("status"=>false,"message" =>"jeton invalide"),200); 
            }
          }else{
            return $response->withJson(array("status"=>$cle_secrete,"message" =>"creation failed  !!!"),200); 
          }

        }catch(Exception $e){
          return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
        }
    }else{
      return $response->withJson(array("status"=>false,"message" =>"paramétre manquant"),200); 
    }

} 


public function cashinFreeMoney(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    if(isset($_POST['commande']) && isset($_POST['dateh']) &&isset($_POST['hmac']) &&isset($_POST['algo']) &&isset($_POST['identifiant']) &&isset($_POST['ref_commande']) &&isset($_POST['site']) &&isset($_POST['total']) &&isset($_POST['numero']) &&isset($_POST['service']) ){
       
        $commande = $request->getParsedBody()['commande'];
        $dateh = $request->getParsedBody()['dateh'];
        $hmac = $request->getParsedBody()['hmac'];
        $algo = $request->getParsedBody()['algo'];
        $identifiant = $request->getParsedBody()['identifiant'];
        $ref_commande = $request->getParsedBody()['ref_commande'];
        $site = $request->getParsedBody()['site'];
        $total = $request->getParsedBody()['total'];
        $numero = $request->getParsedBody()['numero'];
        $service = $request->getParsedBody()['service'];

        try{
           $req=$this->bdd->prepare("SELECT `cle_secrete` FROM `users` WHERE md5(`identifiant`) = :identifiant AND md5(`site`) = :site LIMIT 1");
            $status=$req->execute(array(
              ":identifiant" => $identifiant,
              ":site" => $site,
            ));
            $cle_secrete =$req->fetch();


          if($cle_secrete){
            $cle_bin = pack("H*", $cle_secrete['cle_secrete']);

            $message = "S2M_COMMANDE=$commande"."&S2M_DATEH=$dateh"."&S2M_HTYPE=$algo"."&S2M_IDENTIFIANT=$identifiant"."&S2M_REF_COMMANDE=$ref_commande"."&S2M_SITE=$site"."&S2M_TOTAL=$total";
            $myhmac = strtoupper(hash_hmac(strtolower($algo),$message, $cle_bin));
            //return $response->withJson(array("myhmac"=>$myhmac,"hmac" =>$hmac),200);
            if(strcmp($hmac, $myhmac) == 0){
              $req=$this->bdd->prepare("INSERT INTO `operations`(`id`, `identifiant`, `ref_commande`, `commande`, `date_commande`, `numero`, `montant`, `service`, `etat`) VALUES (null, :identifiant, :ref_commande, :commande, :date_commande, :numero, :montant, :service,0)");
              $st=$req->execute(array(
              
               ":identifiant" =>$identifiant,
               ":ref_commande" =>$ref_commande,
               ":commande" =>$commande,
               ":date_commande" =>$dateh,
               ":numero" =>$numero,
               ":montant" =>$total,
               ":service" =>$service,
              ));       
              $lastInserted = $this->bdd->lastInsertId();
              if($st){
                //$nextOne  =   file_get_contents("files/nextOne.txt");
                /*$file = fopen("files/request/".$lastInserted.".txt", 'w')or die("Unable to open file!");
                fwrite($file, $numero."###".$service."###".$total."###".$ref_commande."###".$commande) ;
                fclose($file);
                
                return $response->withJson(array("status"=>$st,"message" =>"opération encours","idTransaction"=>$lastInserted),200); */
                  $curl = curl_init();

                  curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://d7b7a10d32c7.ngrok.io/api/cashin",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>json_encode(array('amount' => $total,"customermsisdn"=>"221".$numero)),
                    CURLOPT_HTTPHEADER => array(
                      "Content-Type: application/json"
                    ),
                  ));

                  $response = curl_exec($curl);

                  echo $response;
                  $err = curl_error($curl);
                  curl_close($curl);
                  if ($err) {
                    return "cURL Error #:" . $err;
                  } else {
                    return $response;
                  }
                 $file = fopen("files/res.txt", 'w')or die("Unable to open file!");
                  fwrite($file, $response) ;
                  fclose($file);
              }else{
                return $response->withJson(array("status"=>$st,"message" =>"opération echec"),200); 
              }

            }else{
              return $response->withJson(array("status"=>false,"message" =>"jeton invalide"),200); 
            }
          }else{
            return $response->withJson(array("status"=>$cle_secrete,"message" =>"creation failed  !!!"),200); 
          }

        }catch(Exception $e){
          return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
        }

    } else{
      return $response->withJson(array("status"=>false,"message" =>"paramétre manquant"),200); 
    }
} 

public function PMFreeMoney(Request $request, Response $response, $args){
    header("Access-Control-Allow-Origin: *");
    if(isset($_POST['commande']) && isset($_POST['dateh']) &&isset($_POST['hmac']) &&isset($_POST['algo']) &&isset($_POST['identifiant']) &&isset($_POST['ref_commande']) &&isset($_POST['site']) &&isset($_POST['total']) &&isset($_POST['numero']) &&isset($_POST['service']) ){
       
        $commande = $request->getParsedBody()['commande'];
        $dateh = $request->getParsedBody()['dateh'];
        $hmac = $request->getParsedBody()['hmac'];
        $algo = $request->getParsedBody()['algo'];
        $identifiant = $request->getParsedBody()['identifiant'];
        $ref_commande = $request->getParsedBody()['ref_commande'];
        $site = $request->getParsedBody()['site'];
        $total = $request->getParsedBody()['total'];
        $numero = $request->getParsedBody()['numero'];
        $service = $request->getParsedBody()['service'];

        try{
           $req=$this->bdd->prepare("SELECT `cle_secrete` FROM `users` WHERE md5(`identifiant`) = :identifiant AND md5(`site`) = :site LIMIT 1");
            $status=$req->execute(array(
              ":identifiant" => $identifiant,
              ":site" => $site,
            ));
            $cle_secrete =$req->fetch();


          if($cle_secrete){
            $cle_bin = pack("H*", $cle_secrete['cle_secrete']);

            $message = "S2M_COMMANDE=$commande"."&S2M_DATEH=$dateh"."&S2M_HTYPE=$algo"."&S2M_IDENTIFIANT=$identifiant"."&S2M_REF_COMMANDE=$ref_commande"."&S2M_SITE=$site"."&S2M_TOTAL=$total";
            $myhmac = strtoupper(hash_hmac(strtolower($algo),$message, $cle_bin));
            //return $response->withJson(array("myhmac"=>$myhmac,"hmac" =>$hmac),200);
            if(strcmp($hmac, $myhmac) == 0){
              $req=$this->bdd->prepare("INSERT INTO `operations`(`id`, `identifiant`, `ref_commande`, `commande`, `date_commande`, `numero`, `montant`, `service`, `etat`) VALUES (null, :identifiant, :ref_commande, :commande, :date_commande, :numero, :montant, :service,0)");
              $st=$req->execute(array(
              
               ":identifiant" =>$identifiant,
               ":ref_commande" =>$ref_commande,
               ":commande" =>$commande,
               ":date_commande" =>$dateh,
               ":numero" =>$numero,
               ":montant" =>$total,
               ":service" =>$service,
              ));       
              $lastInserted = $this->bdd->lastInsertId();
              if($st){
                //$nextOne  =   file_get_contents("files/nextOne.txt");
                /*$file = fopen("files/request/".$lastInserted.".txt", 'w')or die("Unable to open file!");
                fwrite($file, $numero."###".$service."###".$total."###".$ref_commande."###".$commande) ;
                fclose($file);
                
                return $response->withJson(array("status"=>$st,"message" =>"opération encours","idTransaction"=>$lastInserted),200); */
                  $curl = curl_init();

                  curl_setopt_array($curl, array(
                    CURLOPT_URL => "http://51cf2be6f615.ngrok.io/api/marchandfree",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS =>json_encode(array('amount' => $total,"customermsisdn"=>"221".$numero,"externaltransactionid"=>$lastInserted)),
                    CURLOPT_HTTPHEADER => array(
                      "Content-Type: application/json"
                    ),
                  ));

                  $response = curl_exec($curl);

                  echo $response;
                  $err = curl_error($curl);
                  curl_close($curl);
                  if ($err) {
                    return "cURL Error #:" . $err;
                  } else {
                    return $response;
                  }
                 $file = fopen("files/res.txt", 'w')or die("Unable to open file!");
                  fwrite($file, $response) ;
                  fclose($file);
              }else{
                return $response->withJson(array("status"=>$st,"message" =>"opération echec"),200); 
              }

            }else{
              return $response->withJson(array("status"=>false,"message" =>"jeton invalide"),200); 
            }
          }else{
            return $response->withJson(array("status"=>$cle_secrete,"message" =>"creation failed  !!!"),200); 
          }

        }catch(Exception $e){
          return $response->withJson(array("status"=>false,"message"=>"problem de connection a la base de donnee"));
        }

    } else{
      return $response->withJson(array("status"=>false,"message" =>"paramétre manquant"),200); 
    }
} 
 
}