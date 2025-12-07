<nav class="sticky top-0 z-40 border-b border-white/5 bg-black/60 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 sm:px-10 lg:px-16">
        <a href="{{ route('home') }}" class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-200">"Special Military Operation"  Tracker   </a>

        <div class="hidden items-center gap-3 text-sm text-zinc-200 sm:flex">
            <a href="{{ route('home') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('Home') }}</a>
            <a href="{{ route('articles.index') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('Articles') }}</a>
            <a href="{{ route('war-posts.index') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('SMO Advances') }}</a>
            <a href="{{ route('cities.index') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('Cities') }}</a>
            <a href="{{ route('regions.index') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('Regions') }}</a>
            <a href="{{ route('dashboard') }}" class="rounded-full px-3 py-1 transition hover:text-white hover:bg-white/10">{{ __('Stats') }}</a>
        </div>

        <div class="flex items-center gap-3 sm:hidden">
            <a href="{{ route('articles.index') }}" class="rounded-full border border-emerald-400/40 bg-emerald-500/10 px-3 py-1 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-500/20">{{ __('Articles') }}</a>
            <a href="{{ route('war-posts.index') }}" class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-white transition hover:bg-white/10">{{ __('SMO Advances Reports') }}</a>
        </div>
    </div>
</nav>
