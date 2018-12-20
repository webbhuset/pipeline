<?php

use Webbhuset\Pipeline\Constructor as F;

class UserParser
{
    public function getParser()
    {
        return [
            $this->getCsvReader(),
            $this->getRowValidator(),
            $this->getMapper(),
            $this->getUserIds(),
        ];
    }

    protected function getCsvReader()
    {
        return F::Expand(function(string $filename) {
            $file = fopen($filename);

            $headers = fgetcsv($file);

            while ($row = fgetcsv($file)) {
                yield array_combine($headers, $row);
            }
        });
    }

    protected function getRowValidator()
    {
        return F::Filter(function(array $row) {
            return $row['email'] != '';
        });
    }

    protected function getMapper()
    {
        return F::Map(function(array $row) {
            return [
                'username'  => $row['user'],
                'email'     => $row['email'],
                'name'      => sprintf('%s %s', $row['firstName'], $row['lastName']),
            ];
        });
    }

    protected function getUserIds()
    {
        return [
            F::Chunk(100),
            F::Map(function(array $users) {
                $usernames = array_columns($users, 'username');

                $ids = $this->getUserIdsByUsernames($usernames);

                foreach ($users as $idx => $user) {
                    $users[$idx]['id'] = $ids[$user['username']] ?? null;
                }

                return $users;
            }),
            F::Expand(),
        ];
    }

    protected function getUserIdsByUsernames(array $usernames): array
    {
        // Mock fetching IDs from database
        $id = 0;

        $ids = [];
        foreach ($usernames as $username) {
            if (rand(0, 9)) {
                $id++;
                $ids[$username] = $id;
            }
        }

        return $ids;
    }

        // ...
}
