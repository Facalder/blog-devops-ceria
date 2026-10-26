@props([
    'placeholder' => 'Tulis konten dalam format markdown...',
    'minHeight' => '360px',
    'error' => null,
])

@php
    $wireModelName = $attributes->wire('model')->value() ?: 'content';
@endphp

<div
    x-data="{
        tab: 'write',
        content: @entangle($attributes->wire('model')),
        previewHtml: '',
        init() {
            this.$watch('content', () => {
                if (this.tab === 'preview') {
                    this.renderPreview();
                }
            });
        },
        insert(before, after = '') {
            const textarea = this.$refs.editorTextarea;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const selectedText = textarea.value.substring(start, end);
            const replacement = before + (selectedText || 'teks') + after;
            
            textarea.setRangeText(replacement, start, end, 'select');
            this.content = textarea.value;
            textarea.dispatchEvent(new Event('input', { bubbles: true }));
            textarea.focus();
        },
        renderPreview() {
            if (!this.content) {
                this.previewHtml = '<p class=\'text-tertiary text-sm italic\'>Belum ada konten untuk ditampilkan.</p>';
                return;
            }

            // Lightweight client-side markdown parsing
            let html = this.content
                .replace(/^### (.*$)/gim, '<h3 class=\'font-serif text-lg font-normal text-on-surface mt-4 mb-2\'>$1</h3>')
                .replace(/^## (.*$)/gim, '<h2 class=\'font-serif text-xl font-normal text-on-surface mt-5 mb-2 border-b border-border-subtle pb-1\'>$1</h2>')
                .replace(/^# (.*$)/gim, '<h1 class=\'font-serif text-2xl font-light text-on-surface mt-6 mb-3\'>$1</h1>')
                .replace(/^\> (.*$)/gim, '<blockquote class=\'border-l-2 border-primary pl-3 py-1 my-2 text-tertiary italic\'>$1</blockquote>')
                .replace(/\*\*(.*?)\*\*/gim, '<strong class=\'font-semibold text-on-surface\'>$1</strong>')
                .replace(/\*(.*?)\*/gim, '<em class=\'italic\'>$1</em>')
                .replace(/`([^`]+)`/gim, '<code class=\'font-mono text-xs bg-surface border border-border-subtle px-1.5 py-0.5 rounded text-primary\'>$1</code>')
                .replace(/```([\s\S]*?)```/gim, '<pre class=\'font-mono text-xs bg-surface border border-border-subtle p-3 rounded-[2px] overflow-x-auto my-3\'><code>$1</code></pre>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href=\'$2\' target=\'_blank\' class=\'text-primary underline\'>$1</a>')
                .replace(/^\- (.*$)/gim, '<li class=\'ml-4 list-disc text-sm\'>$1</li>')
                .replace(/^\d+\. (.*$)/gim, '<li class=\'ml-4 list-decimal text-sm\'>$1</li>')
                .replace(/\n\n/gim, '<br/><br/>');

            this.previewHtml = html;
        }
    }"
    class="w-full space-y-2"
>
    {{-- Editor Toolbar & Tabs --}}
    <div class="flex flex-wrap items-center justify-between border border-border-subtle bg-surface px-2.5 py-1.5 rounded-t-[2px]">
        {{-- Formatting Toolbar --}}
        <div class="flex flex-wrap items-center gap-1">
            <button
                type="button"
                @click="insert('**', '**')"
                title="Tebal (Bold)"
                class="px-2 py-1 text-xs font-mono font-bold text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                B
            </button>
            <button
                type="button"
                @click="insert('*', '*')"
                title="Miring (Italic)"
                class="px-2 py-1 text-xs font-mono italic text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                I
            </button>
            <span class="text-border-subtle">|</span>
            <button
                type="button"
                @click="insert('## ')"
                title="Heading 2"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                H2
            </button>
            <button
                type="button"
                @click="insert('### ')"
                title="Heading 3"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                H3
            </button>
            <span class="text-border-subtle">|</span>
            <button
                type="button"
                @click="insert('> ')"
                title="Kutipan (Quote)"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                &ldquo;&rdquo;
            </button>
            <button
                type="button"
                @click="insert('`', '`')"
                title="Inline Code"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                &lt;/&gt;
            </button>
            <button
                type="button"
                @click="insert('```\n', '\n```')"
                title="Code Block"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                Code
            </button>
            <span class="text-border-subtle">|</span>
            <button
                type="button"
                @click="insert('- ')"
                title="Bullet List"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                &bull; List
            </button>
            <button
                type="button"
                @click="insert('[', '](https://)')"
                title="Tautan (Link)"
                class="px-2 py-1 text-xs font-mono text-on-surface hover:bg-white hover:text-primary rounded-[2px] transition-colors cursor-pointer"
            >
                Link
            </button>
        </div>

        {{-- Write / Preview Tab Switcher --}}
        <div class="flex items-center gap-1 border border-border-subtle bg-white p-0.5 rounded-[2px]">
            <button
                type="button"
                @click="tab = 'write'"
                :class="tab === 'write' ? 'bg-primary text-white' : 'text-tertiary hover:text-on-surface'"
                class="px-2.5 py-0.5 text-xs font-mono rounded-[2px] transition-colors cursor-pointer"
            >
                Tulis
            </button>
            <button
                type="button"
                @click="tab = 'preview'; renderPreview()"
                :class="tab === 'preview' ? 'bg-primary text-white' : 'text-tertiary hover:text-on-surface'"
                class="px-2.5 py-0.5 text-xs font-mono rounded-[2px] transition-colors cursor-pointer"
            >
                Pratinjau
            </button>
        </div>
    </div>

    {{-- Textarea & Preview Areas --}}
    <div class="relative">
        <div x-show="tab === 'write'">
            <textarea
                x-ref="editorTextarea"
                wire:model.defer="{{ $wireModelName }}"
                x-model="content"
                rows="16"
                placeholder="{{ $placeholder }}"
                class="w-full min-h-[360px] bg-white text-on-surface font-mono text-sm leading-relaxed p-4 border border-border-subtle border-t-0 rounded-b-[2px] focus:outline-hidden focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-tertiary"
            ></textarea>
        </div>

        <div
            x-show="tab === 'preview'"
            x-cloak
            class="w-full min-h-[360px] bg-white text-on-surface p-6 border border-border-subtle border-t-0 rounded-b-[2px] overflow-y-auto leading-relaxed"
        >
            <div x-html="previewHtml" class="prose max-w-none text-sm text-on-surface"></div>
        </div>
    </div>

    {{-- Error message outside wire:ignore --}}
    @error($wireModelName)
        <p class="mt-1 text-xs text-[#E45757] font-mono">{{ $message }}</p>
    @enderror
</div>
