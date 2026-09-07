@extends('layouts.dashboard')

@section('content')
<style>
    .annual-report-shell { --report-ink: #172033; --report-muted: #64748b; --report-line: #e2e8f0; }
    .annual-report-shell .report-card { border: 1px solid var(--report-line); box-shadow: 0 10px 30px rgba(15, 23, 42, .05); }
    .annual-report-shell .report-card:hover { border-color: #93c5fd; box-shadow: 0 14px 34px rgba(37, 99, 235, .09); }
    .annual-report-shell .report-action { transition: transform .18s ease, box-shadow .18s ease; }
    .annual-report-shell .report-action:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(15, 23, 42, .12); }
    .annual-report-shell .year-content { transition: max-height .2s ease, opacity .2s ease; }
    .annual-report-shell .year-content.is-collapsed { display: none; }
    .annual-report-shell .period-link { border: 1px solid #e2e8f0; background: #fff; transition: border-color .15s ease, background .15s ease, transform .15s ease; }
    .annual-report-shell .period-link:hover { border-color: #86efac; background: #f0fdf4; transform: translateX(2px); }
    .annual-report-shell .summary-link { border: 1px solid #bfdbfe; background: #eff6ff; }
    .annual-report-shell .sort-icon { display: inline-flex; height: 2.25rem; width: 2.25rem; align-items: center; justify-content: center; border: 1px solid #e2e8f0; border-radius: .6rem; color: #64748b; background: #fff; transition: color .15s ease, background .15s ease, border-color .15s ease; }
    .annual-report-shell .sort-icon:hover, .annual-report-shell .sort-icon.is-active { border-color: #93c5fd; background: #eff6ff; color: #2563eb; }
    .annual-report-shell .year-header { cursor: pointer; user-select: none; }
    .annual-report-shell .year-header:focus-visible { outline: 3px solid rgba(37, 99, 235, .2); outline-offset: -3px; }
    .annual-report-shell .year-chevron { transition: transform .2s ease; }
    .annual-report-shell .year-chevron.is-collapsed { transform: rotate(-90deg); }
</style>
<div class="annual-report-shell flex flex-col gap-6">
    <div class="flex items-center justify-between gap-4">
        <div><h1 class="text-2xl font-bold text-gray-900">SBFP Annual Consolidated Report</h1><p class="mt-1 text-sm text-gray-500">DepEd SNS Form 1 baseline aggregate reporting by school year.</p></div>
        <a href="{{ route('admin.school-years.index') }}" class="report-action rounded-lg bg-blue-600 px-3 py-2.5 text-xs font-semibold text-white hover:bg-blue-700"><i class="fas fa-calendar-plus mr-1"></i> Add School Year</a>
    </div>
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm" role="alert">
            <div class="flex items-start gap-3"><i class="fas fa-circle-exclamation mt-0.5"></i><div><strong>Report period not created</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        </div>
    @endif
    <div class="flex flex-col gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm sm:flex-row">
        <input id="annualYearSearch" type="search" placeholder="Search school year..." class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none sm:max-w-xs">
        <div class="flex items-center gap-1" aria-label="Sort school years">
            <button id="annualSortAsc" type="button" class="sort-icon" title="Oldest first" aria-label="Sort oldest first"><i class="fas fa-arrow-up"></i></button>
            <button id="annualSortDesc" type="button" class="sort-icon is-active" title="Newest first" aria-label="Sort newest first"><i class="fas fa-arrow-down"></i></button>
        </div>
        <button id="annualToggleYears" type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Collapse all</button>
    </div>

    @forelse($schoolYears as $schoolYear)
    <section data-annual-year="{{ $schoolYear->year }}" class="report-year-card report-card rounded-xl bg-white">
        <div class="year-header flex flex-col gap-3 border-b border-gray-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between" tabindex="0" role="button" aria-expanded="true" aria-controls="year-content-{{ $schoolYear->id }}">
            <div class="flex items-center gap-3"><span class="year-toggle p-1 text-slate-500" aria-hidden="true"><i class="year-chevron fas fa-chevron-down"></i></span><div><h2 class="font-bold text-gray-900">SY {{ $schoolYear->year }}</h2><p class="text-xs text-gray-500">{{ $schoolYear->reportPeriods->count() }} baseline period(s)</p></div></div>
            <div class="flex flex-wrap gap-2" onclick="event.stopPropagation()"><button type="button" onclick="document.getElementById('period-{{ $schoolYear->id }}').classList.toggle('hidden')" class="report-action rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">+ Add Report Period</button><button type="button" onclick="document.getElementById('edit-school-year-{{ $schoolYear->id }}').classList.remove('hidden')" class="report-action rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white"><i class="fas fa-pen mr-1"></i>Edit</button><form method="POST" action="{{ Auth::user()->role === 'admin' ? route('admin.school-years.destroy', $schoolYear) : route('super-admin.school-years.destroy', $schoolYear) }}" class="inline" onsubmit="return confirm('Delete this school year and all related records?')">@csrf @method('DELETE')<button type="submit" class="report-action rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white" {{ $schoolYear->is_active ? 'disabled title=Activate another school year first' : '' }}><i class="fas fa-trash mr-1"></i>Delete</button></form></div>
        </div>
        <div id="year-content-{{ $schoolYear->id }}" class="year-content">
        <form id="period-{{ $schoolYear->id }}" method="POST" action="{{ route('admin.reports.sbfp.annual.periods.store') }}" class="hidden grid gap-3 border-b border-gray-200 bg-emerald-50 p-4 sm:grid-cols-4">
            @csrf <input type="hidden" name="school_year_id" value="{{ $schoolYear->id }}"><label class="sr-only" for="term-{{ $schoolYear->id }}">Measurement term</label><select id="term-{{ $schoolYear->id }}" name="measurement_period" required class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"><option value="">Select term</option><option value="baseline">Baseline</option><option value="mid">Midline</option><option value="end">Endline</option></select><label class="sr-only" for="month-{{ $schoolYear->id }}">Report month</label><select id="month-{{ $schoolYear->id }}" name="month" required class="rounded-lg border border-emerald-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"><option value="">Select month</option>@foreach(range(1, 12) as $month)<option value="{{ $month }}">{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>@endforeach</select><button class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-800">Create Empty Period</button>
        </form>
        <div class="space-y-2 p-4">
            @forelse($schoolYear->reportPeriods as $period)<a href="{{ route('admin.reports.sbfp.annual.period', $period) }}" class="period-link flex items-center justify-between rounded-lg px-4 py-3 text-sm"><span class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700"><i class="fas fa-calendar-day"></i></span><span><strong class="block text-slate-800">{{ ucfirst($period->measurement_period) }} · {{ $period->name }}</strong><small class="text-xs text-slate-500">Empty until Auto-Generate is run</small></span></span><i class="fas fa-arrow-right text-slate-400"></i></a>@empty<p class="rounded-lg border border-dashed border-slate-200 px-5 py-5 text-sm text-slate-500">No report periods yet. Add a term above to begin.</p>@endforelse
            <a href="{{ route('admin.reports.sbfp.annual.summary', $schoolYear) }}" class="summary-link flex items-center justify-between rounded-lg px-4 py-3 text-sm font-semibold text-blue-800 hover:bg-blue-100"><span class="flex items-center gap-3"><span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700"><i class="fas fa-chart-column"></i></span><span><strong class="block">Annual Summary</strong><small class="font-normal text-xs text-blue-600">Combined report for this school year</small></span></span><i class="fas fa-arrow-right text-blue-500"></i></a>
        </div>
        </div>
        <div id="edit-school-year-{{ $schoolYear->id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/40 p-4" onclick="if(event.target === this) this.classList.add('hidden')">
            <form method="POST" action="{{ Auth::user()->role === 'admin' ? route('admin.school-years.update', $schoolYear) : route('super-admin.school-years.update', $schoolYear) }}" class="w-full max-w-md rounded-xl bg-white p-6 text-left shadow-2xl" onclick="event.stopPropagation()">
                @csrf @method('PATCH')
                <div class="mb-5 flex items-center justify-between"><div><h3 class="font-bold text-slate-900">Update School Year</h3><p class="mt-1 text-xs text-slate-500">Edit the year or adjust its dates.</p></div><button type="button" onclick="document.getElementById('edit-school-year-{{ $schoolYear->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-700" aria-label="Close"><i class="fas fa-times"></i></button></div>
                <label class="mb-1 block text-xs font-semibold text-slate-700">School Year</label><input name="school_year" value="{{ $schoolYear->year }}" pattern="[0-9]{4}-[0-9]{4}" maxlength="9" required class="mb-3 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <label class="mb-1 block text-xs font-semibold text-slate-700">Start Date</label><input type="date" name="start_date" value="{{ optional($schoolYear->start_date)->format('Y-m-d') }}" required class="mb-3 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <label class="mb-1 block text-xs font-semibold text-slate-700">End Date <span class="font-normal text-slate-400">(optional)</span></label><input type="date" name="end_date" value="{{ optional($schoolYear->end_date)->format('Y-m-d') }}" class="mb-5 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('edit-school-year-{{ $schoolYear->id }}').classList.add('hidden')" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600">Cancel</button><button class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white">Save Update</button></div>
            </form>
        </div>
    </section>
    @empty
    <div class="report-card rounded-xl bg-white px-6 py-16 text-center"><i class="fas fa-calendar-alt text-4xl text-blue-200"></i><h2 class="mt-4 font-bold text-gray-800">No school years created</h2><p class="mt-1 text-sm text-gray-500">Create a school year before adding a baseline report period.</p><a href="{{ route('admin.school-years.index') }}" class="report-action mt-5 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Add School Year</a></div>
    @endforelse
</div>
<script>
    const annualSearch = document.getElementById('annualYearSearch');
    const annualSortAsc = document.getElementById('annualSortAsc');
    const annualSortDesc = document.getElementById('annualSortDesc');
    const annualToggle = document.getElementById('annualToggleYears');
    const annualContainer = document.querySelector('.annual-report-shell');
    const annualCards = () => [...annualContainer.querySelectorAll('.report-year-card')];
    const filterAnnualYears = () => { const query = annualSearch.value.trim().toLowerCase(); annualCards().forEach((card) => { card.hidden = !card.dataset.annualYear.toLowerCase().includes(query); }); };
    const sortAnnualYears = (direction) => { const cards = annualCards().sort((first, second) => direction === 'asc' ? first.dataset.annualYear.localeCompare(second.dataset.annualYear) : second.dataset.annualYear.localeCompare(first.dataset.annualYear)); cards.forEach((card) => annualContainer.appendChild(card)); annualSortAsc.classList.toggle('is-active', direction === 'asc'); annualSortDesc.classList.toggle('is-active', direction === 'desc'); };
    annualSearch?.addEventListener('input', filterAnnualYears);
    annualSortAsc?.addEventListener('click', () => sortAnnualYears('asc'));
    annualSortDesc?.addEventListener('click', () => sortAnnualYears('desc'));
    annualContainer.querySelectorAll('.year-header').forEach((header) => { const toggleYear = () => { const content = document.getElementById(header.getAttribute('aria-controls')); const collapsed = content.classList.toggle('is-collapsed'); header.setAttribute('aria-expanded', String(!collapsed)); header.querySelector('.year-chevron').classList.toggle('is-collapsed', collapsed); }; header.closest('.report-year-card').addEventListener('click', (event) => { if (!event.target.closest('a, button, form, input, select, textarea, details, summary')) toggleYear(); }); header.addEventListener('keydown', (event) => { if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); toggleYear(); } }); });
    annualToggle?.addEventListener('click', () => { const collapse = annualToggle.textContent.trim() === 'Collapse all'; annualCards().forEach((card) => { card.querySelector('.year-content').classList.toggle('is-collapsed', collapse); card.querySelector('.year-chevron').classList.toggle('is-collapsed', collapse); }); annualToggle.textContent = collapse ? 'Expand all' : 'Collapse all'; });
</script>
@endsection
