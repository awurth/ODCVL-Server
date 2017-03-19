<?php

namespace App\Controller;

use App\Model\Location;
use App\Model\Item;
use App\Model\Client;
use Respect\Validation\Validator as V;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LocationController extends Controller
{
    /**
     * Get Location list
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCollection(Request $request, Response $response)
    {
        return $this->ok($response, Location::with('client')->get());
    }

    /**
     * Get one Location
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function get(Request $request, Response $response, $id)
    {
        $location = Location::find($id);

        if (null === $location) {
            throw $this->notFoundException($request, $response);
        }

        return $this->ok($response, $location);
    }
     /**
     * Add Location
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function post(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'date_debut' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'La date de début de location est requise',
                    'date' => 'Veuillez saisir une date valide'
                ]
            ],
            'date_fin' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'Veuillez préciser la date d\'achat',
                    'date' => 'Veuillez saisir une date valide'
                ]
            ],
            'client_id' => [
                'rules' => V::notBlank(),
                'messages' => [
                    'notBlank' => 'Le client est requis'
                ]
            ],
            'status' => [
                'rules' => V::notBlank(),
                'messages' => [
                    'intVal' => 'Statut invalide'
                ]
            ]
        ]);

        $arr_items = $request->getParam('items');

        if ($arr_items) {
            $items = Item::findMany($arr_items)->toArray();
            echo json_encode(value);
        }
        else if ( $request->getParam('status') === "active" )
        {
            $this->validator->addError('items', 'Une location active doit contenir des items');
        }
      

        if ($request->getParam('client_id')) {
            $client = Client::find($request->getParam('client_id'));

            if (null === $client) {
                $this->validator->addError('client_id', 'Client inconnu');
            }
        }

        if ($this->validator->isValid()) {
            $location = new Location([
            'date_debut' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut')),
            'date_fin' => \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin')),
            'status' => $request->getParam('status'),
            'prix' => 0
            ]);

            $location->client()->associate($client);
            $location->save();
            $location->items()->attach($arr_items);
            $location->prix = $location->getTotalPrice();
            $location->save();
            $data = json_decode($location,true);
            return $response->withJson($data, 201);
        }

        return $this->validationErrors($response);
    }

    /**
     * Edit Location
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function put(Request $request, Response $response, $id)
    {
        $location = Location::find($id);

        if (null === $location) {
            throw $this->notFoundException($request, $response);
        }

        $this->validator->validate($request, [
            'date_debut' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'Veuillez préciser la date de début',
                    'date' => '{{name}} n\'est pas une date valide'
                ]
            ],
            'date_fin' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'Veuillez préciser la date de fin',
                    'date' => '{{name}} n\'est pas une date valide'
                ]
            ],
            'created_at' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'Veuillez préciser la date de création',
                    'date' => '{{name}} n\'est pas une date valide'
                ]
            ],
            'updated_at' => [
                'rules' => V::notBlank()->date('d/m/Y'),
                'messages' => [
                    'notBlank' => 'Veuillez préciser la date de mise à jour',
                    'date' => '{{name}} n\'est pas une date valide'
                ]
            ],
            'status' => [
                'rules' => V::intVal(),
                'messages' => [
                    'intVal' => 'Statut invalide'
                ]
            ],
            'client_id' => [
                'rules' => V::notBlank(),
                'messages' => [
                    'notBlank' => 'Le client est requis'
                ]
            ]
        ]);

        if ($request->getParam('client_id')) {
            $client = Client::find($request->getParam('client_id'));

            if (null === $client) {
                $this->validator->addError('client_id', 'Client inconnu');
            }
        }

        if ($this->validator->isValid()) {
            $location->date_debut = \DateTime::createFromFormat('d/m/Y', $request->getParam('date_debut'));
            $location->date_fin = \DateTime::createFromFormat('d/m/Y', $request->getParam('date_fin'));
            $location->created_at = \DateTime::createFromFormat('d/m/Y', $request->getParam('created_at'));
            $location->updated_at = \DateTime::createFromFormat('d/m/Y', $request->getParam('updated_at'));
            $location->status = $request->getParam('status');
            $location->client()->associate($client);
            $location->save();

            return $this->noContent($response);
        }

        return $this->validationErrors($response);
    }


    /**
     * Delete location
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function delete(Request $request, Response $response, $id)
    {
        $location = Location::find($id);

        if (null === $location) {
            throw $this->notFoundException($request, $response);
        }

        $location->delete();

        return $this->noContent($response);
    }
}
