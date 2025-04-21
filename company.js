export default class CompanyInfo{
    constructor(){
        this.faqsUrl = "/Scriptz/BackEnd/getFAQs.php";
        this.aboutUrl = "/Scriptz/BackEnd/getAboutUs.php";
        this.contactUrl = "/Scriptz/BackEnd/getContactUs.php";
    }

    async _sendRequest(payload, successMessage) {
        try {
            const response = await fetch(this.faqsUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error(`Server responded with status ${response.status}`);

            const data = await response.json();
            return { success: true, data, message: successMessage };
        } catch (error) {
            console.error(`Error in action "${payload.action}":`, error);
            return { success: false, data: [], message: error.message };
        }
    }

    async _sendRequestAboutUs(payload, successMessage) {
        try {
            const response = await fetch(this.aboutUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error(`Server responded with status ${response.status}`);

            const data = await response.json();
            return { success: true, data, message: successMessage };
        } catch (error) {
            console.error(`Error in action "${payload.action}":`, error);
            return { success: false, data: [], message: error.message };
        }
    }

    async _sendRequestContactUs(payload, successMessage) {
        try {
            const response = await fetch(this.contactUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) throw new Error(`Server responded with status ${response.status}`);

            const data = await response.json();
            return { success: true, data, message: successMessage };
        } catch (error) {
            console.error(`Error in action "${payload.action}":`, error);
            return { success: false, data: [], message: error.message };
        }
    }
    
    //Faq related methods
    async addFaqs(question, answer, creationDate){
        const payload = {
            action: 'create',
            question,
            answer,
            creationDate
        };
        return await this._sendRequest(payload, 'Faq added succesfully');
    }
    async getFaqs(){
        const payload = { action: 'read'};
        return await this._sendRequest(payload, 'Faqs fetched succesfully');
    }
    async updateFaqs(faqID, question, answer, creationDate){
        const payload = {
            action: 'update',
            faqID,
            question,
            answer,
            creationDate
        };
        return await this._sendRequest(payload, 'Faq updated succesfully');
    }
    async deleteFaqs(faqID){
        const payload = {
            action: 'delete',
            faqID
        };
        return await this._sendRequest(payload, 'Faq deleted succesfully');
    }

    //About Us related methods
    async addAboutUs(detail){
        const payload = {
            action: 'create',
            detail
        };
        return await this._sendRequestAboutUs(payload, 'About us added succesfully');
    }
    async getAboutUs(){
        const payload = { action: 'read'};
        return await this._sendRequestAboutUs(payload, 'About us fetched succesfully');
    }
    async updateAboutUs(aboutID, detail){
        const payload = {
            action: 'update',
            aboutID,
            detail
        };
        return await this._sendRequestAboutUs(payload, 'About us updated succesfully');
    }

    //Contact us related methods
    async addContacts(phone, email, instagram, facebook, poBox){
        const payload = {
            action: 'create',
            phone,
            email,
            instagram,
            facebook,
            poBox
        };
        return await this._sendRequestContactUs(payload, 'Contact added succesfully');
    }
    async getContacts(){
        const payload = { action: 'read'};
        return await this._sendRequestContactUs(payload, 'Contacts fetched succesfully');
    }
    async updateContacts(contactID, phone, email, instagram, facebook, poBox){
        const payload = {
            action: 'update',
            contactID,
            phone,
            email,
            instagram,
            facebook,
            poBox
        }
        return await this._sendRequestContactUs(payload, 'Contacts updated succesfully');
    }
    async deleteContacts(contactID){
        const payload = {
            action: 'delete',
            contactID
        };
        return await this._sendRequestContactUs(payload, 'Contacts deleted succesfully');
    }
}