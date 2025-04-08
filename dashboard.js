export default class Dashboard {
    constructor(userID = null) {
        this.userID = userID; // Ensure userID is passed if needed
        this.dashboardData = null; // Store data to prevent multiple fetches
    }

    // Fetch dashboard data once and cache it
    async fetchDashboardData() {
        if (this.dashboardData) return this.dashboardData; // Return cached data

        try {
            const response = await fetch("/Scriptz/BackEnd/dashboardData.php");
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);

            this.dashboardData = await response.json();
            return this.dashboardData;
        } catch (error) {
            console.error("Error fetching dashboard data:", error);
            return {};
        }
    }

    // Filter data for clients
    async getClientDashboardData(clientID) {
        const data = await this.fetchDashboardData();
        if (!data.recentJobs || !data.recentPayments || !data.recentOrders || !data.recentFeedback) {
            console.warn("No client dashboard data available");
            return {};
        }

        return {
            recentJobs: data.recentJobs.filter(job => job.ClientID === clientID),
            recentPayments: data.recentPayments.filter(pay => pay.ClientID === clientID),
            recentOrders: data.recentOrders.filter(order => order.ClientID === clientID),
            recentFeedback: data.recentFeedback.filter(feed => feed.RegID === clientID)
        };
    }

    // Filter data for plumbers
    async getPlumberDashboardData(plumberID) {
        const data = await this.fetchDashboardData();
        if (!data.recentJobAllocations || !data.recentWorkerPayments || !data.recentToolAllocations) {
            console.warn("No plumber dashboard data available");
            return {};
        }

        return {
            recentJobAllocations: data.recentJobAllocations.filter(job => job.RegID === plumberID),
            recentWorkerPayments: data.recentWorkerPayments.filter(pay => pay.RegID === plumberID),
            recentTools: data.recentToolAllocations.filter(tool => tool.RegID === plumberID)
        };
    }

    // Filter data for masons
    async getMasonDashboardData(masonID) {
        const data = await this.fetchDashboardData();
        if (!data.recentJobAllocations || !data.recentWorkerPayments || !data.recentToolAllocations) {
            console.warn("No mason dashboard data available");
            return {};
        }

        return {
            recentJobAllocations: data.recentJobAllocations.filter(job => job.RegID === masonID),
            recentWorkerPayments: data.recentWorkerPayments.filter(pay => pay.RegID === masonID),
            recentToolAllocations: data.recentToolAllocations.filter(tool => tool.RegID === masonID)
        };
    }

    // Filter data for managers
    async getManagerDashboardData() {
        const data = await this.fetchDashboardData();
        if (!data.recentJobs || !data.recentPayments || !data.recentOrders || !data.recentFeedback) {
            console.warn("Incomplete dashboard data received");
            return {};
        }

        return {
            recentJobs: data.recentJobs.filter(job => job.JobStatus === "Pending"),
            recentPayments: data.recentPayments.filter(pay => pay.PaymentStatus === "Completed"),
            recentOrders: data.recentOrders.filter(order => order.Paid === "YES"),
            recentFeedback: data.recentFeedback.filter(feed => feed.Response === null)
        };
    }
}
