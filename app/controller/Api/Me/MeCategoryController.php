<?php
namespace Controller\Api\Me;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Controller\BaseController;
use Engine\Internal\Delivery;
use Service\Me\MeCategoryService;

use Repository\Model\Category;
use Repository\Model\User;

class MeCategoryController extends BaseController {

	public function __construct () {
		$this->delivery = new Delivery;
	}

	public function GetCategoryAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$service = new MeCategoryService($request, $this->delivery);
		$result = $service->getCategory($user, new Category);
		return $this->deliverJSON($response, $result);
	}

	public function CreateCategoryAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$data['id_user'] = $user->id;
		$service = new MeCategoryService($request, $this->delivery);
		$result = $service->createCategory($data, $user, new Category, new User);
		return $this->deliverJSON($response, $result);
	}

	public function UpdateCategoryAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$categoryId = $args['category_id'];
		$service = new MeCategoryService($request, $this->delivery);
		$result = $service->updateCategory($categoryId, $user, $data, new Category, new User);
		return $this->deliverJSON($response, $result);
	}

	public function DeleteCategoryAction (Request $request, Response $response, $args) {
		$user = $this->getUser($request);
		$data = $request->getParsedBody();
		$categoryId = $args['category_id'];
		$service = new MeCategoryService($request, $this->delivery);
		$result = $service->updateCategory($categoryId, $user, ['deleted_at' => date('Y-m-d H:i:s')], new Category, new User);
		return $this->deliverJSON($response, $result);
	}
	
}