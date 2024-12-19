<style>
    div {
        display: flex;
        justify-content: center;
    }

    table {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
    }

    td {
        text-align: center;
        width: 120pt;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    th:nth-child(even), td:nth-child(even) {
        background-color: #e0e0e0;
    }

    @page {
        margin: 10px;
    }
</style>
<div>
    <table>
        <thead>
            <tr>
                <th>ID Pelanggan</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal</th>
                <th>ID Produk</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>QTY</th>
                <th>Subtotal</th>
                <th>Grandtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $firstRow = true;
                foreach ($pelanggan as $plg => $value) { ?>
                <tr>
                    <?php if ($firstRow) { ?>
                        <td rowspan="<?php echo count($pelanggan); ?>"><?php echo $value['id_pelanggan']; ?></td>
                        <td rowspan="<?php echo count($pelanggan); ?>"><?php echo $value['nama_pelanggan']; ?></td>
                    <?php
                        $firstRow = false;
                    } ?>
                    <td><?php echo $value['tanggal']; ?></td>
                    <td><?php echo $value['id_produk']; ?></td>
                    <td><?php echo $value['nama_produk']; ?></td>
                    <td><?php echo $value['harga']; ?></td>
                    <td><?php echo $value['quantity']; ?></td>
                    <td><?php echo $value['subtotal']; ?></td>
                    <?php
                    $firstRow = true;
                    if ($firstRow) { ?>
                        <td rowspan="<?php echo count($pelanggan); ?>"><?php echo $value['grandtotal']; ?></td>
                    <?php
                        $firstRow = false;
                    } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>