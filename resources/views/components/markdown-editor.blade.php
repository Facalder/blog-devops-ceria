@props([
    'wireModelName' => 'content',
    'placeholder' => 'Tulis konten dalam format Markdown...',
])

<div
    wire:ignore
    x-data="{
        tab: 'write',
        content: @entangle($wireModelName),
        previewHtml: '',
        insert(prefix, suffix = '') {
            const textarea = this.$refs.editorTextarea;
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selected = text.substring(start, end);

            const replacement = prefix + selected + suffix;
            textarea.value = text.substring(0, start) + replacement + text.substring(end);

            this.content = textarea.value;

            textarea.focus();
            const cursorPos = start + prefix.length + selected.length;
            textarea.setSelectionRange(cursorPos, cursorPos);
        },
        renderPreview() {
            if (!this.content || this.content.trim() === '') {
                this.previewHtml = '<p class=\'text-tertiary italic text-sm\'>Belum ada konten untuk dipratinjau.</p>';
                return;
            }

            let text = this.content
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            let html = text
                .replace(/```([\s\S]*?)```/g, '<pre class=\'bg-surface p-3 rounded font-mono text-xs overflow-x-auto my-3 border border-border-subtle\'><code>$1</code></pre>')
                .replace(/`([^`]+)`/g, '<code class=\'bg-surface px-1.5 py-0.5 rounded font-mono text-xs text-primary border border-border-subtle\'>$1</code>')
                .replace(/^### (.*$)/gim, '<h3 class=\'font-serif text-lg font-normal text-on-surface mt-4 mb-2\'>$1</h3>')
                .replace(/^## (.*$)/gim, '<h2 class=\'font-serif text-xl font-normal text-on-surface mt-6 mb-2 border-b border-border-subtle pb-1\'>$1</h2>')
                .replace(/^# (.*$)/gim, '<h1 class=\'font-serif text-2xl font-light text-on-surface mt-6 mb-3\'>$1</h1>')
                .replace(/\*\*([^*]+)\*\*/g, '<strong class=\'font-semibold text-on-surface\'>$1</strong>')
                .replace(/\*([^*]+)\*/g, '<em class=\'italic\'>$1</em>')
                .replace(/^> (.*$)/gim, '<blockquote class=\'border-l-2 border-primary pl-3 my-2 text-on-surface/80 italic\'>$1</blockquote>')
                .replace(/^\- (.*$)/gim, '<li class=\'ml-4 list-disc text-on-surface\'>$1</li>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href=\'$2\' target=\'_blank\' rel=\'noopener\' class=\'text-primary underline hover:text-primary-hover\'>$1</a>')
                .replace(/\n\n/g, '<p class=\'my-3 text-on-surface leading-relaxed\'></p>')
                .replace(/\n/g, '<br />');

            this.previewHtml = html;
        }
    }"
    class="w-full space-y-2"
>
    <div class="flex flex-wrap items-center justify-between border border-border-subtle bg-surface px-2.5 py-1.5 rounded-t-[2px]">
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

    @error($wireModelName)
        <p class="mt-1 text-xs text-[#E45757] font-mono">{{ $message }}</p>
    @enderror
</div>
