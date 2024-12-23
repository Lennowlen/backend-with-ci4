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
<div class="container-table">
    <table>
        <thead>
            <tr>
                <th style="text-align: start;">#</th>
                <th>Tanggal</th>
                <th>id</th>
                <th>Nama Pelanggan</th>
                <th>total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            foreach ($data as $index => $value) { ?>
            <!-- <?php var_dump($value['tanggal']) ?> -->
                <tr>
                    <td><?php echo $index + 1 ?></td>
                    <td><?php echo $value['tanggal']; ?></td>
                    <td style="text-align: start;"><?php echo $value['id']; ?></td>
                    <td><?php echo $value['nama']; ?></td>
                    <td><?php echo $formatter->formatCurrency($value['grandtotal'], 'IDR'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>