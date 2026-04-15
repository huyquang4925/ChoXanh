<?php
require_once __DIR__.'/../core/Model.php';

class CartModel extends Model {
    protected $table = 'cart_items';

    public function getOrCreateCart($user_id) {
        $user_id = intval($user_id);
        $sql = "SELECT id FROM carts WHERE user_id = $user_id LIMIT 1";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc()['id'];
        }

        $this->conn->query("INSERT INTO carts (user_id) VALUES ($user_id)");
        return $this->conn->insert_id;
    }

    public function addItem($cart_id, $product_id, $quantity) {
        $cart_id = intval($cart_id);
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        $sql = "SELECT id, quantity FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $item = $result->fetch_assoc();
            $new_quantity = $item['quantity'] + $quantity;
            return $this->conn->query("UPDATE cart_items SET quantity = $new_quantity WHERE id = " . $item['id']);
        }

        
        $sql_insert = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cart_id, $product_id, $quantity)";
        return $this->conn->query($sql_insert);
    }

    public function updateQuantity($cart_item_id, $quantity) {
        $id = intval($cart_item_id);
        $qty = intval($quantity);
       
        $sql = "UPDATE cart_items SET quantity = $qty WHERE id = $id";
        return $this->conn->query($sql);
    }

    public function removeItem($id) {
        $id = intval($id);
        $sql = "DELETE FROM cart_items WHERE id = $id";
        return $this->conn->query($sql);
    }

    public function getItems($cart_id) {
        $cart_id = intval($cart_id);
        $sql = "SELECT ci.id AS cart_item_id, ci.quantity, p.name, p.price, p.image 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.id 
                WHERE ci.cart_id = $cart_id";
        
        $result = $this->conn->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}