<?php

class RadioController
{
    public function nowPlaying(array $params, array $body): void
    {
        // Proxy AzuraCast's Now Playing API to avoid CORS issues in app.
        // Until AzuraCast is provisioned, AZURACAST_URL stays unset and we
        // skip straight to the static fallback below (e.g. a free hosted
        // stream URL in STREAM_URL such as Zeno.fm).
        $azuraUrl  = rtrim($_ENV['AZURACAST_URL'] ?? '', '/');
        $stationId = $_ENV['AZURACAST_STATION'] ?? '1';

        $json = $azuraUrl ? @file_get_contents("$azuraUrl/api/nowplaying/$stationId") : false;

        if ($json) {
            $data = json_decode($json, true);
            Response::json([
                'stream_url'  => STREAM_URL,
                'is_online'   => $data['station']['is_public'] ?? true,
                'now_playing' => [
                    'title'    => $data['now_playing']['song']['title'] ?? 'Live Stream',
                    'artist'   => $data['now_playing']['song']['artist'] ?? 'Ministry Radio',
                    'art'      => $data['now_playing']['song']['art'] ?? null,
                ],
                'listeners'   => $data['listeners']['current'] ?? 0,
            ]);
        } else {
            // Fallback if AzuraCast unreachable — report offline rather than
            // falsely claiming a live stream.
            Response::json([
                'stream_url'  => STREAM_URL,
                'is_online'   => false,
                'now_playing' => ['title' => 'Ministry Radio', 'artist' => '', 'art' => null],
                'listeners'   => 0,
            ]);
        }
    }

    public function schedule(array $params, array $body): void
    {
        $db   = Database::connect();
        $rows = $db->query("SELECT * FROM radio_schedule WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
        Response::json($rows);
    }

    public function updateSchedule(array $params, array $body): void
    {
        $db = Database::connect();

        if (!empty($params['id'])) {
            $stmt = $db->prepare('
                UPDATE radio_schedule
                SET day_of_week = :day, start_time = :start, end_time = :end,
                    program_name = :program, description = :description, is_active = :active
                WHERE id = :id
            ');
            $stmt->execute([
                ':day'         => $body['day_of_week'],
                ':start'       => $body['start_time'],
                ':end'         => $body['end_time'],
                ':program'     => $body['program_name'],
                ':description' => $body['description'] ?? null,
                ':active'      => isset($body['is_active']) ? (int)$body['is_active'] : 1,
                ':id'          => $params['id'],
            ]);
        } else {
            $stmt = $db->prepare('
                INSERT INTO radio_schedule (day_of_week, start_time, end_time, program_name, description)
                VALUES (:day, :start, :end, :program, :description)
            ');
            $stmt->execute([
                ':day'         => $body['day_of_week'],
                ':start'       => $body['start_time'],
                ':end'         => $body['end_time'],
                ':program'     => $body['program_name'],
                ':description' => $body['description'] ?? null,
            ]);
        }

        Response::json(['saved' => true]);
    }
}
