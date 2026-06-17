<?php

class GivingController
{
    public function initiate(array $params, array $body): void
    {
        $db  = Database::connect();
        $ref = 'MIN-' . strtoupper(uniqid());

        $stmt = $db->prepare('INSERT INTO givings (reference, amount, currency, giving_type, donor_name, donor_email, campaign_id) VALUES (:ref, :amount, :currency, :type, :name, :email, :campaign_id)');
        $stmt->execute([
            ':ref'         => $ref,
            ':amount'      => $body['amount'],
            ':currency'    => $body['currency'] ?? 'USD',
            ':type'        => $body['giving_type'] ?? 'offering',
            ':name'        => $body['donor_name'] ?? null,
            ':email'       => $body['donor_email'] ?? null,
            ':campaign_id' => $body['campaign_id'] ?? null,
        ]);

        $appUrl = $_ENV['APP_URL'] ?? 'https://roberttalemwa.online';
        Response::json([
            'reference'    => $ref,
            'amount'       => $body['amount'],
            'currency'     => $body['currency'] ?? 'USD',
            'redirect_url' => $appUrl . '/give/thanks',
        ], 201);
    }

    public function webhook(array $params, array $body): void
    {
        $db     = Database::connect();
        $ref    = $body['txRef'] ?? $body['tx_ref'] ?? $body['data']['tx_ref'] ?? '';
        $status = ($body['status'] === 'successful' || ($body['data']['status'] ?? '') === 'successful')
                  ? 'completed' : 'failed';

        $stmt = $db->prepare('UPDATE givings SET status = :status WHERE reference = :ref');
        $stmt->execute([':status' => $status, ':ref' => $ref]);

        // Update campaign raised_amount if linked
        if ($status === 'completed') {
            $giving = $db->prepare('SELECT campaign_id, amount FROM givings WHERE reference = :ref');
            $giving->execute([':ref' => $ref]);
            $row = $giving->fetch();

            if ($row && $row['campaign_id']) {
                $db->prepare('UPDATE campaigns SET raised_amount = raised_amount + :amount WHERE id = :id')
                   ->execute([':amount' => $row['amount'], ':id' => $row['campaign_id']]);
            }
        }

        Response::json(['received' => true]);
    }

    public function index(array $params, array $body): void
    {
        $db     = Database::connect();
        $where  = ['1=1'];
        $binds  = [];

        if (!empty($_GET['currency'])) {
            $where[] = 'currency = :currency';
            $binds[':currency'] = $_GET['currency'];
        }
        if (!empty($_GET['giving_type'])) {
            $where[] = 'giving_type = :giving_type';
            $binds[':giving_type'] = $_GET['giving_type'];
        }
        if (!empty($_GET['status'])) {
            $where[] = 'status = :status';
            $binds[':status'] = $_GET['status'];
        }
        if (!empty($_GET['from'])) {
            $where[] = 'created_at >= :from';
            $binds[':from'] = $_GET['from'];
        }
        if (!empty($_GET['to'])) {
            $where[] = 'created_at <= :to';
            $binds[':to'] = $_GET['to'];
        }

        $sql  = 'SELECT * FROM givings WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT 200';
        $stmt = $db->prepare($sql);
        $stmt->execute($binds);
        Response::json($stmt->fetchAll());
    }

    public function summary(array $params, array $body): void
    {
        $db = Database::connect();

        $byCurrency = $db->query("
            SELECT currency, SUM(amount) as total, COUNT(*) as count
            FROM givings WHERE status = 'completed'
            GROUP BY currency
        ")->fetchAll();

        $byType = $db->query("
            SELECT giving_type, currency, SUM(amount) as total, COUNT(*) as count
            FROM givings WHERE status = 'completed'
            GROUP BY giving_type, currency
        ")->fetchAll();

        $byMonth = $db->query("
            SELECT strftime('%Y-%m', created_at) as month, currency, SUM(amount) as total
            FROM givings WHERE status = 'completed'
            GROUP BY month, currency
            ORDER BY month DESC
            LIMIT 12
        ")->fetchAll();

        Response::json([
            'by_currency' => $byCurrency,
            'by_type'     => $byType,
            'by_month'    => $byMonth,
        ]);
    }
}
