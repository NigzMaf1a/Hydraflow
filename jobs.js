export default class Jobs {
    constructor() {
        this.jobs = [];
    }

    async getAllJobs(method = "GET", bodyData = null) {
        try {
            const options = {
                method,
                headers: {
                    "Content-Type": "application/json"
                }
            };
    
            // Include body only for POST requests
            if (method === "POST" && bodyData) {
                options.body = JSON.stringify(bodyData);
            }
    
            const response = await fetch('/HydraFlow/Scriptz/BackEnd/getJobs.php', options);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            this.jobs = await response.json();
            return this.jobs; // Ensure data is returned
        } catch (error) {
            console.error("Error fetching all jobs:", error);
            return []; // Return an empty array if an error occurs
        }
    }
    

    getUnapprovedJobs() {
        return this.jobs.filter(job => job.Approved === "NO");
    }
    getApprovedJobs() {
        return this.jobs.filter(job => job.Approved === "YES");
    }

    getIncompleteJobs() {
        return this.jobs.filter(job => job.JobStatus === "Pending");
    }
    getCompleteJobs(){
        return this.jobs.filter(job => job.JobStatus === "Completed");
    }
    getRepairJobs(){
        return this.jobs.filter(job => job.JobType === "Repair");
    }
    getInstallationJobs(){
        return this.jobs.filter(job => job.JobType === "Installation");
    }
    getUnpaidJobs(){
        return this.jobs.filter(job => job.Paid === "NO");
    }
    getPaidJobs(){
        return this.jobs.filter(job => job.Paid === "YES");
    }
}
