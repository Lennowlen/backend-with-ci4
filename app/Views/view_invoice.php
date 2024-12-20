<style>
    div.container-header table tr {
        background: none;
    }

    div.container-table {
        display: flex;
        justify-content: center;
        margin: 0 auto;
        border: 2px solid #333;
    }

    div.container-table table {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
    }

    div.container-table table tr th,
    div.container-table table tr td {
        border: 1px solid #333;
        padding: 5px;
        text-align: center;
        word-wrap: break-word;
    }

    div.container-table table tr.total {
        background: none;
    }

    tr:nth-child(even) {
        background-color: #e0e0e0;
    }

    div.container-header {
        font-family: Arial, Helvetica, sans-serif;
        text-align: center;
        margin-bottom: 20px;
    }

    @page {
        margin: 20px;
    }
</style>
<div class="container-header">
    <h2>Invoice</h2>
    <table>
        <tbody>
            <tr>
                <td>Nama Pelanggan : <?php echo $pelanggan[0]['nama_pelanggan']; ?></td>
            </tr>
            <tr>
                <td>Tanggal : <?php echo $pelanggan[0]['tanggal']; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="container-table">
    <table>
        <thead>
            <tr>
                <th style="text-align: start;">#</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>QTY</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            foreach ($pelanggan as $plg => $value) { ?>
                <tr>
                    <td style="text-align: start;"><?php echo $value['id']; ?></td>
                    <td><?php echo $value['nama_produk']; ?></td>
                    <td><?php echo $formatter->formatCurrency($value['harga'], 'IDR'); ?></td>
                    <td><?php echo $value['quantity']; ?></td>
                    <td><?php echo $formatter->formatCurrency($value['subtotal'], 'IDR'); ?></td>
                </tr>
            <?php } ?>

            <?php
            $firstRow = true;
            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            foreach ($grandtotal as $plg => $value) { ?>
                <tr class="total" >
                    <?php if ($firstRow) { ?>
                        <td colspan="4"><b>Total : </b></td>
                        <td colspan="1"><b><?php echo $formatter->formatCurrency($value['total'], 'IDR'); ?></b></td>
                    <?php
                        $firstRow = false;
                    } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>