<?php
namespace Container;

use Container\Exceptions\ContainerException;
use Container\Exceptions\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $entityCallableCollection = [];

    /**
     * @var array
     */
    private $entityInstanceCollection = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('ID not found in container (%s)', $id));
        }
        if (array_key_exists($id, $this->entityInstanceCollection)) {
            return $this->entityInstanceCollection[$id];
        }
        $this->entityInstanceCollection[$id] = $this->entityCallableCollection[$id]($this);

        return $this->entityInstanceCollection[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->entityCallableCollection);
    }

    /**
     * @param string   $id
     * @param callable $callable
     * @throws ContainerException
     */
    public function set(string $id, callable $callable) {
        $this->validateIdNotEmpty($id);
        $this->validateIdNotAlreadyAnInstance($id);
        $this->entityCallableCollection[$id] = $callable;
    }

    /**
     * @param string $id
     * @throws ContainerException
     */
    private function validateIdNotEmpty(string $id)
    {
        if (empty(trim($id))) {
            throw new ContainerException('Cannot supply an empty ID');
        }
    }

    /**
     * @param string $id
     * @throws ContainerException
     */
    private function validateIdNotAlreadyAnInstance(string $id)
    {
        if (array_key_exists($id, $this->entityInstanceCollection)) {
            throw new ContainerException(sprintf('Id (%s) is already set', $id));
        }
    }
}