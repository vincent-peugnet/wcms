<?php

namespace Wcms;

use RuntimeException;

class Servicesession
{
    public function setvisitor(bool $visitor): void
    {
        $_SESSION['visitor'] = $visitor;
    }

    public function getvisitor(): bool
    {
        return $_SESSION['visitor'] ?? true;
    }

    public function setuser(string $userid): void
    {
        $_SESSION['user'] = $userid;
    }

    public function getuser(): ?string
    {
        return $_SESSION['user'] ?? null;
    }

    public function setwsessionid(string $wsessionid): void
    {
        $_SESSION['wsession'] = $wsessionid;
    }

    public function getwsessionid(): string
    {
        return $_SESSION['wsession'] ?? '';
    }

    /**
     * @param array<string, mixed> $opt
     */
    public function setopt(array $opt): void
    {
        $_SESSION['opt'] = $opt;
    }

    /**
     * @return array<string, mixed>
     */
    public function getopt(): array
    {
        return $_SESSION['opt'] ?? [];
    }

    public function setworkspace(Workspace $workspace): void
    {
        $_SESSION['workspace'] = $workspace;
    }

    /**
     * @throws RuntimeException if Workspace is not present in session cookie.
     */
    public function getworkspace(): Workspace
    {
        if (isset($_SESSION['workspace']) && $_SESSION['workspace'] instanceof Workspace) {
            return $_SESSION['workspace'];
        } else {
            throw new RuntimeException('no available workspace in session');
        }
    }

    public function setgraph(Graph $graph): void
    {
        $_SESSION['graph'] = $graph;
    }

    public function getgraph(): Graph
    {
        if (isset($_SESSION['graph']) && $_SESSION['graph'] instanceof Graph) {
            return $_SESSION['graph'];
        } else {
            return new Graph();
        }
    }

    /**
     * Empty current user session
     */
    public function empty(): void
    {
        $_SESSION = [];
    }
}
