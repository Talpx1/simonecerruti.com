<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;
use function Safe\mkdir;
use function Safe\readlink;
use function Safe\symlink;

afterEach(function (): void {
    File::deleteDirectory($this->project);
});

describe('ai:link-hooks', function (): void {
    it('links .ai/hooks into .claude, creating the folder', function (): void {
        $this->project = createAiHooksProject();

        expect(\file_exists("{$this->project}/.claude"))->toBeFalse();

        artisan('ai:link-hooks', ['--base-path' => $this->project])
            ->expectsOutputToContain('.claude/hooks -> .ai/hooks')
            ->assertExitCode(0);

        expect(\is_link("{$this->project}/.claude/hooks"))->toBeTrue()
            ->and(readlink("{$this->project}/.claude/hooks"))->toBe('../.ai/hooks')
            ->and("{$this->project}/.claude/hooks/guard.sh")->toBeFile()
            ->and(\file_exists("{$this->project}/.codex"))->toBeFalse();
    });

    it('links .codex too when the folder already exists', function (): void {
        $this->project = createAiHooksProject();
        mkdir("{$this->project}/.codex");

        expect(\is_link("{$this->project}/.codex/hooks"))->toBeFalse();

        artisan('ai:link-hooks', ['--base-path' => $this->project])->assertExitCode(0);

        expect(\is_link("{$this->project}/.codex/hooks"))->toBeTrue();
    });

    it('replaces a link that points somewhere else', function (): void {
        $this->project = createAiHooksProject();
        mkdir("{$this->project}/.claude");
        symlink('../elsewhere', "{$this->project}/.claude/hooks");

        expect(readlink("{$this->project}/.claude/hooks"))->toBe('../elsewhere');

        artisan('ai:link-hooks', ['--base-path' => $this->project])->assertExitCode(0);

        expect(readlink("{$this->project}/.claude/hooks"))->toBe('../.ai/hooks');
    });

    it('refuses to replace a real folder', function (): void {
        $this->project = createAiHooksProject();
        mkdir("{$this->project}/.claude/hooks", 0755, true);

        artisan('ai:link-hooks', ['--base-path' => $this->project])
            ->expectsOutputToContain('is a real folder')
            ->assertFailed();

        expect(\is_link("{$this->project}/.claude/hooks"))->toBeFalse();
    });

    it('fails when there is nothing to link', function (): void {
        $this->project = createAiHooksProject(has_hooks_folder: false);

        artisan('ai:link-hooks', ['--base-path' => $this->project])
            ->expectsOutputToContain('There is no .ai/hooks folder to link.')
            ->assertFailed();

        expect(\file_exists("{$this->project}/.claude"))->toBeFalse();
    });
});
