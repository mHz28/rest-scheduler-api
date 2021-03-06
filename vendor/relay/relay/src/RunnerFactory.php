<?php
/**
 *
 * This file is part of Relay for PHP.
 *
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @copyright 2015, Paul M. Jones
 *
 */
namespace Relay;

/**
 *
 * A factory to create (and re-create) Runner objects.
 *
 * @package Relay.Relay
 *
 */
class RunnerFactory
{
    /**
     *
     * The middleware queue.
     *
     * @var (callable|mixed|MiddlewareInterface)[]
     *
     */
    protected $queue = [];

    /**
     *
     * A callable to convert queue entries to callables.
     *
     * @var callable|ResolverInterface
     *
     */
    protected $resolver;

    /**
     *
     * Constructor.
     *
     * @param (callable|mixed|MiddlewareInterface)[] $queue The middleware queue.
     *
     * @param callable|ResolverInterface $resolver Converts queue entries to
     *                                             callable|MiddlewareInterface
     *
     * @return self
     *
     */
    public function __construct(array $queue, $resolver = null)
    {
        $this->queue = $queue;
        $this->resolver = $resolver;
    }

    /**
     *
     * Creates a new Runner.
     *
     * @return Runner
     *
     */
    public function newInstance()
    {
        return new Runner($this->queue, $this->resolver);
    }
}
