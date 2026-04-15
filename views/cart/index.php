<div class="container mt-4">
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($items)): ?>
        <div class="alert alert-info">Giỏ hàng đang trống.</div>
        <a href="index.php?page=home" class="btn btn-primary">Mua sắm ngay</a>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo htmlspecialchars($it['image'] ?? 'default.jpg'); ?>" style="width:60px; height:60px; object-fit:cover; margin-right:10px;">
                            <?php echo htmlspecialchars($it['name'] ?? ''); ?>
                        </td>
                        <td><?php echo number_format($it['price'] ?? 0); ?>đ</td>
                        <td>
                            <form method = "POST" action = "index.php?page=cart_update" style = "display: flex; gap: 5px;">
                                <input type = "hidden" name ="cart_item_id" value ="<?php echo $it['cart_item_id'] ; ?>">
                                <input type ="number" name = "quantity" value ="<?php echo $it['quantity']; ?>" min="1" style ="width :70px" class ="form-control">
                                <button type ="submit" class = "btn btn-primary">Lưu</button>
                            </form>
                        </td>
                        <td class="font-weight-bold text-primary">
                            <?php echo number_format(($it['price'] ?? 0) * ($it['quantity'] ?? 0)); ?>đ
                        </td>
                        <td>
                        <form method="POST" action="index.php?page=cart_remove">
                            <input type="hidden" name="cart_item_id" value="<?php echo $it['cart_item_id']; ?>">
                            
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="row mt-4 align-items-center">
            <div class="col-md-6">
                <a href="index.php?page=home" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left"></i> Tiếp tục mua hàng
                </a>
            </div>
            <div class="col-md-6 text-right">
                <h4 class="mb-3">Tổng cộng: <span class="text-danger"><?php echo number_format($total ?? 0); ?>đ</span></h4>
                <a href="index.php?page=checkout" class="btn btn-success btn-lg px-5">Thanh toán</a>
            </div>
        </div>
    <?php endif; ?>
</div>