<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Api.php';
require_once __DIR__ . '/../models/ProductModel.php';

class ProductApi extends Api {
    private $productModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new ProductModel();
    }

    public function handleRequest() {
        $action = $this->getParam('action');

        switch ($action) {
            case 'list':
                $limit   = $this->getParam('limit',  null);
                $offset  = $this->getParam('offset', null);
                $orderBy = $this->getParam('order',  'id DESC');

                $limit  = $limit  !== '' ? intval($limit)  : null;
                $offset = $offset !== '' ? intval($offset) : null;

                $products = $this->productModel->getProductsWithCategory($limit, $offset, $orderBy);
                $this->response(true, $products);
                break;

            case 'get':
                $id = intval($this->getParam('id', 0));
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }
                $product = $this->productModel->getProductDetail($id);
                if ($product) {
                    $this->response(true, $product);
                } else {
                    $this->response(false, [], "Không tìm thấy sản phẩm");
                }
                break;

            case 'search':
                $keyword = trim($this->getParam('keyword', ''));
                if (empty($keyword)) {
                    $this->response(false, [], "Vui lòng nhập từ khóa tìm kiếm");
                }
                $limit  = $this->getParam('limit',  null);
                $offset = $this->getParam('offset', null);
                $limit  = $limit  !== '' ? intval($limit)  : null;
                $offset = $offset !== '' ? intval($offset) : null;

                $products = $this->productModel->searchProducts($keyword, $limit, $offset);
                $total    = $this->productModel->countSearchResults($keyword);
                $this->response(true, [
                    'total'    => $total,
                    'products' => $products
                ]);
                break;

            case 'by_category':
                $categoryId = intval($this->getParam('category_id', 0));
                if ($categoryId <= 0) {
                    $this->response(false, [], "category_id không hợp lệ");
                }
                $keyword  = trim($this->getParam('keyword', ''));
                $products = $this->productModel->getByCategory($categoryId, $keyword);
                $this->response(true, $products);
                break;

            case 'by_manufacturer':
                $manufacturerId = intval($this->getParam('manufacturer_id', 0));
                if ($manufacturerId <= 0) {
                    $this->response(false, [], "manufacturer_id không hợp lệ");
                }
                $products = $this->productModel->getByManufacturer($manufacturerId);
                $this->response(true, $products);
                break;

            case 'add':
                $name            = trim($this->input['name']            ?? '');
                $price           = $this->input['price']           ?? null;
                $stock           = $this->input['stock']           ?? 0;
                $categoryId      = intval($this->input['category_id']    ?? 0);
                $manufacturerId  = intval($this->input['manufacturer_id'] ?? 0);
                $description     = trim($this->input['description']  ?? '');
                $image           = trim($this->input['image']        ?? '');

                if (empty($name) || $price === null) {
                    $this->response(false, [], "Vui lòng điền đầy đủ tên và giá sản phẩm");
                }

                $data = [
                    'name'            => $name,
                    'price'           => floatval($price),
                    'stock'           => intval($stock),
                    'category_id'     => $categoryId > 0 ? $categoryId : null,
                    'manufacturer_id' => $manufacturerId > 0 ? $manufacturerId : null,
                    'description'     => $description,
                    'image'           => $image
                ];

                $newId = $this->productModel->addProduct($data);
                if ($newId) {
                    $this->response(true, ['id' => $newId], "Thêm sản phẩm thành công");
                } else {
                    $this->response(false, [], "Thêm sản phẩm thất bại");
                }
                break;

            case 'update':
                $id = intval($this->input['id'] ?? 0);
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }

                $data = [];
                if (isset($this->input['name']))            $data['name']            = trim($this->input['name']);
                if (isset($this->input['price']))           $data['price']           = floatval($this->input['price']);
                if (isset($this->input['stock']))           $data['stock']           = intval($this->input['stock']);
                if (isset($this->input['category_id']))     $data['category_id']     = intval($this->input['category_id']);
                if (isset($this->input['manufacturer_id'])) $data['manufacturer_id'] = intval($this->input['manufacturer_id']);
                if (isset($this->input['description']))     $data['description']     = trim($this->input['description']);
                if (isset($this->input['image']))           $data['image']           = trim($this->input['image']);

                if (empty($data)) {
                    $this->response(false, [], "Không có dữ liệu để cập nhật");
                }

                $success = $this->productModel->updateProduct($id, $data);
                $this->response($success, [], $success ? "Cập nhật sản phẩm thành công" : "Cập nhật sản phẩm thất bại");
                break;

            case 'delete':
                $id = intval($this->input['id'] ?? 0);
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }

                $success = $this->productModel->deleteProduct($id);
                $this->response($success, [], $success ? "Xóa sản phẩm thành công" : "Xóa sản phẩm thất bại");
                break;

            default:
                $this->response(false, [], "Hành động không hợp lệ");
                break;
        }
    }
}

$api = new ProductApi();
$api->handleRequest();
