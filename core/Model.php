<?php
class Model {
    protected $db;
    protected $conn;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    // Hàm đếm tổng số bản ghi
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return intval($row['total']);
        }
        return 0;
    }

    // Lấy tất cả bản ghi (Hỗ trợ cả findAll và all)
    public function findAll() {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->conn->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function all() { return $this->findAll(); }

    // Tìm theo ID (Hỗ trợ cả find và findById để sửa lỗi của bạn)
    public function find($id) {
        $id = intval($id);
        $sql = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = $this->conn->query($sql);
        return ($result) ? $result->fetch_assoc() : null;
    }
    public function findById($id) { return $this->find($id); }

    // Thêm mới
    public function insert($data) {
        $keys = array_keys($data);
        $values = array_map(function($v) {
            return "'" . $this->conn->real_escape_string($v) . "'";
        }, array_values($data));
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ")";
        
        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        }
        return false;
    }

    // Xóa
    public function delete($id) {
        $id = intval($id);
        $sql = "DELETE FROM {$this->table} WHERE id = $id";
        return $this->conn->query($sql);
    }
    
    // Cập nhật
    public function update($id, $data) {
        $id = intval($id);
        $sets = [];
        foreach ($data as $key => $value) {
            $sets[] = "$key = '" . $this->conn->real_escape_string($value) . "'";
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = $id";
        return $this->conn->query($sql);
    }
}