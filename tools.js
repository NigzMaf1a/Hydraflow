export default class Tools {
    constructor() {
        this.tools = [];
    }

    async getTools(method = 'GET', body = null) {
        try {
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            
            if (method === 'POST' && body) {
                options.body = JSON.stringify(body);
            }
            
            const response = await fetch('/Scriptz/BackEnd/getTools.php', options);
            
            if (!response.ok) {
                throw new Error(`Failed to fetch tools with ${method} method`);
            }
            
            this.tools = await response.json();
            return this.tools;
        } catch (error) {
            console.error(`Failed to fetch tools: ${error.message}`);
            return [];
        }
    }
    
}
