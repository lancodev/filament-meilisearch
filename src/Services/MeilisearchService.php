<?php

namespace Lancodev\FilamentMeilisearch\Services;

use Meilisearch\Client;
use Meilisearch\Contracts\IndexesQuery;
use Meilisearch\Endpoints\Indexes;
use Meilisearch\Endpoints\Keys;

class MeilisearchService
{
    protected Client $client;

    public function __construct(
        protected string $host,
        protected ?string $apiKey = null,
    ) {
        $this->client = new Client($this->host, $this->apiKey);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getHealth(): array
    {
        return $this->client->health();
    }

    public function getVersion(): array
    {
        return $this->client->version();
    }

    public function getStats(): array
    {
        return $this->client->stats();
    }

    public function getIndexes(array $options = []): array
    {
        $query = new IndexesQuery();

        if (isset($options['limit'])) {
            $query->setLimit($options['limit']);
        }

        if (isset($options['offset'])) {
            $query->setOffset($options['offset']);
        }

        $results = $this->client->getIndexes($query)->getResults();

        return array_map(fn (Indexes $index) => [
            'uid' => $index->getUid(),
            'primaryKey' => $index->getPrimaryKey(),
            'createdAt' => $index->getCreatedAt()?->format('c'),
            'updatedAt' => $index->getUpdatedAt()?->format('c'),
        ], $results);
    }

    public function getIndex(string $uid): Indexes
    {
        return $this->client->index($uid);
    }

    public function createIndex(string $uid, ?string $primaryKey = null): array
    {
        return $this->client->createIndex($uid, ['primaryKey' => $primaryKey]);
    }

    public function deleteIndex(string $uid): array
    {
        return $this->client->deleteIndex($uid);
    }

    public function swapIndexes(array $indexes): array
    {
        return $this->client->swapIndexes($indexes);
    }

    public function getDocuments(string $indexUid, array $options = []): array
    {
        $index = $this->client->index($indexUid);

        $query = new \Meilisearch\Contracts\DocumentsQuery();

        if (isset($options['limit'])) {
            $query->setLimit($options['limit']);
        }

        if (isset($options['offset'])) {
            $query->setOffset($options['offset']);
        }

        if (isset($options['fields'])) {
            $query->setFields($options['fields']);
        }

        return $index->getDocuments($query)->toArray();
    }

    public function getDocument(string $indexUid, string|int $documentId): array
    {
        $index = $this->client->index($indexUid);

        return $index->getDocument($documentId);
    }

    public function addDocuments(string $indexUid, array $documents, ?string $primaryKey = null): array
    {
        $index = $this->client->index($indexUid);

        return $index->addDocuments($documents, $primaryKey);
    }

    public function updateDocuments(string $indexUid, array $documents, ?string $primaryKey = null): array
    {
        $index = $this->client->index($indexUid);

        return $index->updateDocuments($documents, $primaryKey);
    }

    public function deleteDocument(string $indexUid, string|int $documentId): array
    {
        $index = $this->client->index($indexUid);

        return $index->deleteDocument($documentId);
    }

    public function deleteDocuments(string $indexUid, array $documentIds): array
    {
        $index = $this->client->index($indexUid);

        return $index->deleteDocuments($documentIds);
    }

    public function deleteAllDocuments(string $indexUid): array
    {
        $index = $this->client->index($indexUid);

        return $index->deleteAllDocuments();
    }

    public function search(string $indexUid, string $query, array $options = []): array
    {
        $index = $this->client->index($indexUid);

        return $index->search($query, $options)->toArray();
    }

    public function getKeys(array $options = []): array
    {
        $query = new \Meilisearch\Contracts\KeysQuery();

        if (isset($options['limit'])) {
            $query->setLimit($options['limit']);
        }

        if (isset($options['offset'])) {
            $query->setOffset($options['offset']);
        }

        $results = $this->client->getKeys($query)->getResults();

        return array_map(fn (Keys $key) => [
            'uid' => $key->getUid(),
            'name' => $key->getName(),
            'key' => $key->getKey(),
            'description' => $key->getDescription(),
            'actions' => $key->getActions(),
            'indexes' => $key->getIndexes(),
            'expiresAt' => $key->getExpiresAt()?->format('c'),
            'createdAt' => $key->getCreatedAt()?->format('c'),
            'updatedAt' => $key->getUpdatedAt()?->format('c'),
        ], $results);
    }

    public function getKey(string $keyOrUid): array
    {
        $key = $this->client->getKey($keyOrUid);

        return [
            'uid' => $key->getUid(),
            'name' => $key->getName(),
            'key' => $key->getKey(),
            'description' => $key->getDescription(),
            'actions' => $key->getActions(),
            'indexes' => $key->getIndexes(),
            'expiresAt' => $key->getExpiresAt()?->format('c'),
            'createdAt' => $key->getCreatedAt()?->format('c'),
            'updatedAt' => $key->getUpdatedAt()?->format('c'),
        ];
    }

    public function createKey(array $options): array
    {
        $key = $this->client->createKey($options);

        return [
            'uid' => $key->getUid(),
            'name' => $key->getName(),
            'key' => $key->getKey(),
            'description' => $key->getDescription(),
            'actions' => $key->getActions(),
            'indexes' => $key->getIndexes(),
            'expiresAt' => $key->getExpiresAt()?->format('c'),
            'createdAt' => $key->getCreatedAt()?->format('c'),
            'updatedAt' => $key->getUpdatedAt()?->format('c'),
        ];
    }

    public function updateKey(string $keyOrUid, array $options): array
    {
        $key = $this->client->updateKey($keyOrUid, $options);

        return [
            'uid' => $key->getUid(),
            'name' => $key->getName(),
            'key' => $key->getKey(),
            'description' => $key->getDescription(),
            'actions' => $key->getActions(),
            'indexes' => $key->getIndexes(),
            'expiresAt' => $key->getExpiresAt()?->format('c'),
            'createdAt' => $key->getCreatedAt()?->format('c'),
            'updatedAt' => $key->getUpdatedAt()?->format('c'),
        ];
    }

    public function deleteKey(string $keyOrUid): array
    {
        return $this->client->deleteKey($keyOrUid);
    }

    public function getTasks(array $options = []): array
    {
        $query = new \Meilisearch\Contracts\TasksQuery();

        if (isset($options['limit'])) {
            $query->setLimit($options['limit']);
        }

        if (isset($options['from'])) {
            $query->setFrom($options['from']);
        }

        if (isset($options['statuses'])) {
            $query->setStatuses($options['statuses']);
        }

        if (isset($options['types'])) {
            $query->setTypes($options['types']);
        }

        if (isset($options['indexUids'])) {
            $query->setIndexUids($options['indexUids']);
        }

        if (isset($options['canceledBy'])) {
            $query->setCanceledBy($options['canceledBy']);
        }

        if (isset($options['beforeEnqueuedAt'])) {
            $query->setBeforeEnqueuedAt($options['beforeEnqueuedAt']);
        }

        if (isset($options['afterEnqueuedAt'])) {
            $query->setAfterEnqueuedAt($options['afterEnqueuedAt']);
        }

        if (isset($options['beforeStartedAt'])) {
            $query->setBeforeStartedAt($options['beforeStartedAt']);
        }

        if (isset($options['afterStartedAt'])) {
            $query->setAfterStartedAt($options['afterStartedAt']);
        }

        if (isset($options['beforeFinishedAt'])) {
            $query->setBeforeFinishedAt($options['beforeFinishedAt']);
        }

        if (isset($options['afterFinishedAt'])) {
            $query->setAfterFinishedAt($options['afterFinishedAt']);
        }

        return $this->client->getTasks($query)->getResults();
    }

    public function getTask(int $taskUid): array
    {
        return $this->client->getTask($taskUid);
    }

    public function cancelTasks(array $options = []): array
    {
        $query = new \Meilisearch\Contracts\CancelTasksQuery();

        if (isset($options['statuses'])) {
            $query->setStatuses($options['statuses']);
        }

        if (isset($options['types'])) {
            $query->setTypes($options['types']);
        }

        if (isset($options['indexUids'])) {
            $query->setIndexUids($options['indexUids']);
        }

        if (isset($options['beforeEnqueuedAt'])) {
            $query->setBeforeEnqueuedAt($options['beforeEnqueuedAt']);
        }

        if (isset($options['afterEnqueuedAt'])) {
            $query->setAfterEnqueuedAt($options['afterEnqueuedAt']);
        }

        if (isset($options['beforeStartedAt'])) {
            $query->setBeforeStartedAt($options['beforeStartedAt']);
        }

        if (isset($options['afterStartedAt'])) {
            $query->setAfterStartedAt($options['afterStartedAt']);
        }

        if (isset($options['beforeFinishedAt'])) {
            $query->setBeforeFinishedAt($options['beforeFinishedAt']);
        }

        if (isset($options['afterFinishedAt'])) {
            $query->setAfterFinishedAt($options['afterFinishedAt']);
        }

        return $this->client->cancelTasks($query);
    }

    public function deleteTasks(array $options = []): array
    {
        $query = new \Meilisearch\Contracts\DeleteTasksQuery();

        if (isset($options['statuses'])) {
            $query->setStatuses($options['statuses']);
        }

        if (isset($options['types'])) {
            $query->setTypes($options['types']);
        }

        if (isset($options['indexUids'])) {
            $query->setIndexUids($options['indexUids']);
        }

        if (isset($options['beforeEnqueuedAt'])) {
            $query->setBeforeEnqueuedAt($options['beforeEnqueuedAt']);
        }

        if (isset($options['afterEnqueuedAt'])) {
            $query->setAfterEnqueuedAt($options['afterEnqueuedAt']);
        }

        if (isset($options['beforeStartedAt'])) {
            $query->setBeforeStartedAt($options['beforeStartedAt']);
        }

        if (isset($options['afterStartedAt'])) {
            $query->setAfterStartedAt($options['afterStartedAt']);
        }

        if (isset($options['beforeFinishedAt'])) {
            $query->setBeforeFinishedAt($options['beforeFinishedAt']);
        }

        if (isset($options['afterFinishedAt'])) {
            $query->setAfterFinishedAt($options['afterFinishedAt']);
        }

        return $this->client->deleteTasks($query);
    }

    public function waitForTask(int $taskUid, array $options = []): array
    {
        return $this->client->waitForTask(
            $taskUid,
            $options['timeoutInMs'] ?? 5000,
            $options['intervalInMs'] ?? 50,
        );
    }

    public function createDump(): array
    {
        return $this->client->createDump();
    }

    public function createSnapshot(): array
    {
        return $this->client->createSnapshot();
    }

    public function getIndexSettings(string $indexUid): array
    {
        $index = $this->client->index($indexUid);

        $settings = $index->getSettings();

        // Deep-convert any nested ArrayObject instances (Synonyms, TypoTolerance,
        // Faceting, Embedders) to plain arrays for Livewire serialization.
        return json_decode(json_encode($settings), true);
    }

    public function updateIndexSettings(string $indexUid, array $settings): array
    {
        $index = $this->client->index($indexUid);

        return $index->updateSettings($settings);
    }

    public function resetIndexSettings(string $indexUid): array
    {
        $index = $this->client->index($indexUid);

        return $index->resetSettings();
    }

    public function getIndexStats(string $indexUid): array
    {
        $index = $this->client->index($indexUid);

        return $index->stats();
    }
}
