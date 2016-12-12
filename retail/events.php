<?php
require './../vendor/autoload.php';
require './../lib/RetailToZadarma.php';

$RetailToZadarma = new \lib\RetailToZadarma();
$data = $RetailToZadarma->Mysql->table('retail')->select('*')->get();

?>
<meta http-equiv="refresh" content="5">
<style>
    table, tr, td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    table {
        width: 100%;
    }

    td {
        padding: 1px 10px;
    }
</style>

<table>
    <tbody>
        <?php if(!empty($data)): ?>
            <?php foreach($data as $Row): ?>
                <tr>
                    <td colspan="3"><?= $Row->call_id ?></td>
                </tr>
                <tr>
                    <td><?= date('H:i', $Row->created_at) ?></td>
                    <td><pre><?= print_r(json_decode($Row->start_data, true), true) ?></pre></td>
                    <td><pre><?= print_r(json_decode($Row->end_data, true), true) ?></pre></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
