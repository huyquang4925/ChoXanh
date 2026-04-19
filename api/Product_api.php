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

            // Lấy danh sách tất cả sản phẩm
            case 'list':
                $products = $this->productModel->getProductsWithCategory();
                $this->response(true, $products);
                break;

            // Lấy chi tiết một sản phẩm theo id
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

            // Tìm kiếm sản phẩm theo từ khóa
            case 'search':
                $keyword  = trim($this->getParam('keyword', ''));
                $products = $this->productModel->searchProducts($keyword);
                $total    = $this->productModel->countSearchResults($keyword);
                $this->response(true, ['total' => $total, 'products' => $products]);
                break;

            // Lấy sản phẩm theo danh mục
            case 'by_category':
                $categoryId = intval($this->getParam('category_id', 0));
                if ($categoryId <= 0) {
                    $this->response(false, [], "category_id không hợp lệ");
                }
                $products = $this->productModel->getByCategory($categoryId);
                $this->response(true, $products);
                break;

            // Thêm sản phẩm mới
            case 'add':
                $name           = trim($this->input['name']            ?? '');
                $price          = $this->input['price']                ?? null;
                $stock          = $this->input['stock']                ?? 0;
                $categoryId     = intval($this->input['category_id']   ?? 0);
                $manufacturerId = intval($this->input['manufacturer_id'] ?? 0);
                $description    = trim($this->input['description']     ?? '');
                $image          = trim($this->input['image']           ?? '');

                if (empty($name) || $price === null) {
                    $this->response(false, [], "Vui lòng điền đầy đủ tên và giá sản phẩm");
                }

                $newId = $this->productModel->addProduct([
                    'name'            => $name,
                    'price'           => floatval($price),
                    'stock'           => intval($stock),
                    'category_id'     => $categoryId,
                    'manufacturer_id' => $manufacturerId,
                    'description'     => $description,
                    'image'           => $image
                ]);

                if ($newId) {
                    $this->response(true, ['id' => $newId], "Thêm sản phẩm thành công");
                } else {
                    $this->response(false, [], "Thêm sản phẩm thất bại");
                }
                break;

            // Cập nhật sản phẩm
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
                $this->response($success, [], $success ? "Cập nhật thành công" : "Cập nhật thất bại");
                break;

            // Xóa sản phẩm
            case 'delete':
                $id = intval($this->input['id'] ?? 0);
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }

                $success = $this->productModel->deleteProduct($id);
                $this->response($success, [], $success ? "Xóa thành công" : "Xóa thất bại");
                break;

            default:
                $this->response(false, [], "Hành động không hợp lệ");
                break;
        }
    }
}

$api = new ProductApi();
$api->handleRequest();
