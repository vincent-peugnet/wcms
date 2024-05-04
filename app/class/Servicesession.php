<?php

namespace Wcms;

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

    public function setopt(array $opt): void
    {
        $_SESSION['opt'] = $opt;
    }

    public function getopt(): array
    {
        return $_SESSION['opt'] ?? [];
    }

    public function setworkspace(Workspace $workspace): void
    {
        $_SESSION['workspace'] = $workspace->dry();
    }

    public function getworkspace(): Workspace
    {
        $datas = $_SESSION['workspace'] ?? [];
        return new Workspace($datas);
    }

    /**
     * Empty current user session
     */
    public function empty(): void
    {
        $_SESSION = [];
    }
}
