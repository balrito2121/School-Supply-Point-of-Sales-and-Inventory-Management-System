<?php
?>
                    <div class="page-intro"><div><h2>Completed sales</h2><p>Review transaction details and sales performance.</p></div><span class="count-badge"><?php echo count($store['transactions']); ?> total sales</span></div>
                    <?php if ($user['role'] === 'admin'): ?>
                    <div class="stats-grid report-stats">
                        <article class="stat-card"><div class="stat-top"><span>Sales today</span><span class="stat-icon orange-icon"><?php echo icon('cash'); ?></span></div><strong><?php echo money($todaySales); ?></strong><small>From <?php echo $todayTransactions; ?> transaction<?php echo $todayTransactions === 1 ? '' : 's'; ?></small></article>
                        <article class="stat-card"><div class="stat-top"><span>All-time revenue</span><span class="stat-icon green-icon"><?php echo icon('reports'); ?></span></div><strong><?php echo money(array_sum(array_map(function ($transaction) { return (float) $transaction['total']; }, $store['transactions']))); ?></strong><small>Completed transactions</small></article>
                        <article class="stat-card"><div class="stat-top"><span>Units sold</span><span class="stat-icon purple-icon"><?php echo icon('inventory'); ?></span></div><strong><?php echo $totalUnits; ?></strong><small>Across all transactions</small></article>
                    </div>
                    <section class="panel">
                        <div class="panel-heading"><div><h3>Best-selling products</h3><p>Ranked by the number of units sold.</p></div></div>
                        <?php if (count($productSales) === 0): ?><div class="empty-state"><span><?php echo icon('reports'); ?></span><strong>No sales data yet</strong><p>Product rankings will appear after the first sale.</p></div><?php else: ?>
                        <div class="table-wrap"><table><thead><tr><th>RANK</th><th>PRODUCT</th><th>UNITS SOLD</th><th>SALES</th></tr></thead><tbody><?php $rank = 0; foreach ($productSales as $sales): $rank++; ?><tr><td><span class="rank-badge"><?php echo $rank; ?></span></td><td><strong><?php echo e($sales['name']); ?></strong></td><td><?php echo (int) $sales['quantity']; ?> units</td><td class="amount"><?php echo money($sales['revenue']); ?></td></tr><?php endforeach; ?></tbody></table></div>
                        <?php endif; ?>
                    </section>
                    <?php endif; ?>
                    <section class="panel">
                        <div class="panel-heading"><div><h3>Transaction history</h3><p>Newest transactions appear first.</p></div></div>
                        <?php if (count($transactions) === 0): ?><div class="empty-state"><span><?php echo icon('receipt'); ?></span><strong>No transactions yet</strong><p>Completed sales will be listed here.</p></div><?php else: ?>
                        <div class="table-wrap"><table class="sales-records-table"><thead><tr><th>TRANSACTION</th><th>DATE &amp; TIME</th><th>PRODUCTS · QTY · PRICE</th><th>CASHIER</th><th>PAYMENT</th><th>TOTAL</th></tr></thead><tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><strong><?php echo e($transaction['id']); ?></strong></td>
                                <td><?php echo e(date('M j, Y · g:i A', strtotime($transaction['date']))); ?></td>
                                <td><div class="sales-item-list"><?php foreach ($transaction['items'] as $item): ?><span><strong><?php echo e($item['name']); ?></strong> · <?php echo (int) $item['quantity']; ?> × <?php echo money($item['price']); ?> = <?php echo money($item['line_total']); ?></span><?php endforeach; ?></div></td>
                                <td><?php echo e($transaction['cashier']); ?></td>
                                <td><span class="payment-tag"><?php echo e($transaction['payment_method']); ?></span></td>
                                <td class="amount"><?php echo money($transaction['total']); ?><small class="change-line">Paid <?php echo money($transaction['amount_paid']); ?> · Change <?php echo money($transaction['change']); ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody></table></div>
                        <?php endif; ?>
                    </section>
