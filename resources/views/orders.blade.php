<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Yönetimi</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial; margin: 30px; }
        input, select { margin: 5px; padding: 5px; }
        button { margin: 5px; padding: 5px 10px; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .status{
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            text-transform: uppercase;
        }
        .status.pending { background: orange; color: white; }
        .status.processing { background: blue; color: white; }
        .status.completed { background: green; color: white; }
        .status.cancelled { background: red; color: white; }
    </style>
</head>
<body>


    <h1>Sipariş Paneli - (Ana Sayfa)</h1>
    <ul id="order-list"></ul>

    <h2 id="form-title">Yeni Sipariş Ekle</h2>
    <form id="order-form">
        <input type="hidden" id="edit_id">

        <select id="customer_id" required></select>
        <select id="product_id" required></select>
        <input type="number" id="quantity" placeholder="Adet" required>
        <select id="status">
            <option value="pending">Bekliyor</option>
            <option value="processing">İşleniyor</option>
            <option value="completed">Tamamlandı</option>
            <option value="cancelled">İptal</option>
        </select>
        <button type="submit">Kaydet</button>
    </form>

    <script>
        // Siparişleri Listele
        function fetchOrders() {
            $.get('/api/orders', function(data) {
                $('#order-list').empty();
                data.forEach(order => {
                    $('#order-list').append(`
                    <li data-id="${order.id}">
                        <strong>${order.customer.name}</strong> - ${order.product.name} (${order.quantity})
                        <span class="status ${order.status}">${order.status}</span>
                        <button class="edit-btn">Güncelle</button>
                        <button class="delete-btn">Sil</button>
                    </li>
                    `);
                });
            });
        }

        // Sipariş Ekle veya Güncelle
        $('#order-form').submit(function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            const method = id ? 'PUT' : 'POST';
            const url = id ? `/api/orders/${id}` : '/api/orders';

            $.ajax({
                url: url,
                method: method,
                contentType: 'application/json',
                data: JSON.stringify({
                    customer_id: parseInt($('#customer_id').val()),
                    product_id: parseInt($('#product_id').val()),
                    quantity: parseInt($('#quantity').val()),
                    status: $('#status').val()
                }),
,
                success: function() {
                    fetchOrders();
                    $('#order-form')[0].reset();
                    $('#edit_id').val('');
                    $('#form-title').text('Yeni Sipariş Ekle');
                }
            });
        });

        // Siparişi Güncellemek için verileri forma doldur
        $(document).on('click', '.edit-btn', function () {
            const li = $(this).closest('li');
            const id = li.data('id');

            $.get(`/api/orders/${id}`, function (order) {
                $('#edit_id').val(order.id);
                $('#quantity').val(order.quantity);
                $('#status').val(order.status);

                // Dropdown'larda ilgili seçenekleri seç
                $('#customer_id').val(order.customer_id);
                $('#product_id').val(order.product_id);

                $('#form-title').text('Siparişi Güncelle');
            });
        });


        // Siparişi Sil
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).closest('li').data('id');
            if (confirm("Bu siparişi silmek istiyor musunuz?")) {
                $.ajax({
                    url: `/api/orders/${id}`,
                    method: 'DELETE',
                    success: function () {
                        fetchOrders();
                    }
                });
            }
        });

        function loadDropdowns() {
        // Müşteri listesi
        $.get('/api/customers', function(customers) {
            $('#customer_id').empty();
            customers.forEach(c => {
                $('#customer_id').append(`<option value="${c.id}">${c.name}</option>`);
            });
        });

        // Ürün listesi
        $.get('/api/products', function(products) {
            $('#product_id').empty();
            products.forEach(p => {
                $('#product_id').append(`<option value="${p.id}">${p.name} - ${p.price} ₺</option>`);
            });
        });
}


        // Sayfa yüklendiğinde siparişleri getir
        $(document).ready(function() {
            fetchOrders();
            loadDropdowns();
        });
    </script>
</body>
</html>
