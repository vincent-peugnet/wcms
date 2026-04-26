<?php

namespace Wcms;

class Servicesession
{
    public function __construct()
    {
        // empty session if W has been updated
        if (!isset($_SESSION['w_version']) || $_SESSION['w_version'] !== getversion()) {
            $this->empty();
            $_SESSION['w_version'] = getversion();
        }
    }

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
     * @return Workspace                    That was stored in session, or create a new one
     */
    public function getworkspace(): Workspace
    {
        if (isset($_SESSION['workspace']) && $_SESSION['workspace'] instanceof Workspace) {
            return $_SESSION['workspace'];
        } else {
            $workspace = new Workspace();
            $_SESSION['workspace'] = $workspace;
            return $workspace;
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
     * Add an alert to a dedicated page.
     * The content will be shown as a JS alert the next time the page is loaded by this reader
     */
    public function addalert(string $pageid, string $content): void
    {
        $_SESSION['alerts'][$pageid] = $content;
    }

    /**
     * Read a potential alert for a dedicated page.
     * The storage is cleared after reading.
     */
    public function consumealert(string $pageid): ?string
    {
        $content = $_SESSION['alerts'][$pageid] ?? null;
        unset($_SESSION['alerts'][$pageid]);
        return $content;
    }

    /**
     * Empty current user session
     */
    public function empty(): void
    {
        $_SESSION = [];
    }
}
