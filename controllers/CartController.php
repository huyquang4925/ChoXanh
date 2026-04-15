<?php
require_once __DIR__ . '/../core/Controller.php';

class CartController extends Controller {
    
    public function index() {
        $this->requireLogin();
        $user_id = $this->getUserId();

        $response = $this->callAPI(
            "GET",
            "http://localhost/btap_lon_web/api/Cart_api.php?action=list&user_id=" . $user_id
        );

        $items = $response['data'] ?? [];
        
        $total = 0;
        foreach ($items as $it) {
            $total += ($it['price'] * $it['quantity']);
        }

        $this->view('cart/index', [
            'items' => $items,
            'total' => $total
        ]);
    }

    public function add() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $this->post('product_id');
            $this->callAPI(
                "POST",
                "http://localhost/btap_lon_web/api/Cart_api.php?action=add",
                [
                    "user_id" => $this->getUserId(),
                    "product_id" => $product_id,
                    "quantity" => 1
                ]
            );
            $this->redirect('index.php?page=cart');
        }
    }

    public function remove() {
        $this->requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $cart_item_id = $this->post('cart_item_id');
          
            $this->callAPI(
                "POST", 
                "http://localhost/btap_lon_web/api/Cart_api.php?action=remove",
                ["cart_item_id" => $cart_item_id]
            );

            $this->redirect('index.php?page=cart');
        }
    }

    public function update(){
        $this->requireLogin();
        if($_SERVER['REQUEST_METHOD']==='POST'){
            $cart_item_id = $this->post('cart_item_id');
            $quantity = $this->post('quantity');
            
            if($cart_item_id && $quantity >0){
                $this->callAPI(
                    "POST",
                    "http://localhost/btap_lon_web/api/Cart_api.php?action=update",
                    ["cart_item_id"=>$cart_item_id, "quantity"=>$quantity]
                );

            }
        }
        $this->redirect('index.php?page=cart');
    }
}
?>