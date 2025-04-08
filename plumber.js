import Jobs from "./jobs";
import Tools from "./tools";
import EmployeePayment from "./employeePayment";

export default class Plumber extends Registration {
    constructor(PlumberID, Name1, Name2, PhoneNo, Email, Password, Gender, accStatus, Balance) {
        super(PlumberID, Name1, Name2, PhoneNo, Email, Password, Gender, accStatus);
        this.Balance = Balance;
        this.jobs = new Jobs();
        this.tools = new Tools();
        this.jobPay = new EmployeePayment();
    }

    async assignedJobs(SessionID) {
        const allJobs = await this.jobs.getAllJobs(); // Corrected method
        return allJobs.filter(job => job.AssignedPlumberID === SessionID);
    }

    async assignedTools(SessionID) {
        const allTools = await this.tools.getTools(); // Fetch first
        return allTools.filter(tool => tool.AssignedPlumberID === SessionID);
    }

    async jobPayment(SessionID) {
        const allPayments = await this.jobPay.getEmployeePayment(); // Fetch first
        return allPayments.filter(payment => payment.EmployeeID === SessionID);
    }
}
