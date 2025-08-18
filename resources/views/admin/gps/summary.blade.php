@extends('admin.layout.base')

@section('title', 'GPs ')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">GP LGD Internet Status Dashboard</h2>

    <!-- Filters -->
    <div class="mb-8 flex flex-wrap items-end gap-4" id="filter-form">
        <div>
            <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
            <input type="date" id="from_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div>
            <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
            <input type="date" id="to_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Search LGD Code</label>
            <input type="text" id="search" placeholder="e.g. 108406" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
        </div>

        <div class="mt-5">
            <button onclick="loadGpData()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Load Summary
            </button>
        </div>
    </div>

    <!-- Chart -->
    <div class="bg-white p-4 rounded-lg shadow mb-10">
        <h4 class="text-lg font-semibold text-gray-700 mb-4">Downtime Frequency by Hour</h4>
        <canvas id="downtimeChart" height="120"></canvas>
    </div>

    <!-- LGD Summary -->
    <div id="lgd-cards" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let chart;

    function loadGpData() {
        const from = document.getElementById('from_date').value;
        const to = document.getElementById('to_date').value;
        const search = document.getElementById('search').value.trim();

        const url = `/public/westbengal/public/admin/gp_summary`;
        const params = new URLSearchParams();
        if (from) params.append('from_date', from);
        if (to) params.append('to_date', to);

        fetch(`${url}?${params.toString()}`)
            .then(res => res.json())
            .then(data => {
                renderCards(data.lgd_analysis, search);
                renderChart(data.chart_data);
            });
    }

    function renderCards(data, search = '') {
        const container = document.getElementById('lgd-cards');
        container.innerHTML = '';

        const filtered = Object.entries(data).filter(([lgd]) =>
            lgd.includes(search)
        );

        if (filtered.length === 0) {
            container.innerHTML = `<p class="text-gray-500">No matching LGD codes found.</p>`;
            return;
        }

        filtered.forEach(([lgd, info]) => {
            const statusClass = info.status === 'active'
                ? 'bg-green-100 text-green-800'
                : 'bg-red-100 text-red-800';

            const ticketStatus = info.latest_ticket.status === 'completed'
                ? 'bg-green-200 text-green-800'
                : 'bg-yellow-200 text-yellow-800';

            container.innerHTML += `
                <div class="bg-white shadow rounded-2xl p-6 border hover:shadow-lg transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-semibold text-gray-700">LGD Code: ${lgd}</h3>
                        <span class="text-sm px-3 py-1 rounded-full font-medium ${statusClass}">
                            ${info.status.charAt(0).toUpperCase() + info.status.slice(1)}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 mb-2">
                        <p><strong>Total Tickets:</strong> ${info.total_tickets}</p>
                        <p><strong>Most Frequent Downtime Hour:</strong> ${info.most_frequent_downtime_hour}:00</p>
                    </div>

                    <div class="mt-4">
                        <h4 class="text-gray-700 font-semibold mb-2">Latest Ticket Info</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li><strong>Ticket ID:</strong> ${info.latest_ticket.ticketid}</li>
                            <li><strong>Status:</strong>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium ${ticketStatus}">
                                    ${info.latest_ticket.status}
                                </span>
                            </li>
                            <li><strong>Downtime:</strong> ${info.latest_ticket.downdate} ${info.latest_ticket.downtime}</li>
                            <li><strong>Reason:</strong> ${info.latest_ticket.downreason}</li>
                            <li><strong>Details:</strong> ${info.latest_ticket.downreasonindetailed}</li>
                        </ul>
                    </div>
                </div>
            `;
        });
    }

    function renderChart(chartData) {
        const ctx = document.getElementById('downtimeChart').getContext('2d');
        const labels = Object.keys(chartData);
        const values = Object.values(chartData);

        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Downtime Events',
                    data: values,
                    backgroundColor: '#4F46E5',
                    borderRadius: 6
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Set default last 30 days
    function setDefaultDates() {
        const to = new Date();
        const from = new Date();
        from.setDate(to.getDate() - 30);

        document.getElementById('from_date').value = from.toISOString().split('T')[0];
        document.getElementById('to_date').value = to.toISOString().split('T')[0];
    }

    // Load on page open
    window.onload = function () {
        setDefaultDates();
        loadGpData();
    };
</script>
@endsection
