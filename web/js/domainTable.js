new Vue({
    el: '#app',
    data: {
        domains: [],
        error: null,
        selectedDomain: null,
        showPopup: false,
        contactDetails: [],
        showContactPopup: false,
        visitors: [],
        showVisitorPopup: false,
    },
    created() {
        this.fetchDomains();
    },
    methods: {
        fetchDomains() {
            fetch('/domain/get-all')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Сеть ответила с ошибкой: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    this.domains = data;
                })
                .catch(error => {
                    this.error = error.message;
                });
        },
        showDomainDetails(domain) {
            this.selectedDomain = domain;
            this.fetchDomainDetails(domain.id);
            this.showPopup = true;
        },
        fetchDomainDetails(domainId) {
            fetch(`/domain/details/${domainId}`)
                .then(response => response.json())
                .then(data => {
                    this.selectedDomain.balance = data.balance;
                    this.selectedDomain.categories = data.categories;
                });
        },
        showContactDetails(domainId) {
            this.selectedDomain = this.domains.find(domain => domain.id === domainId);
            fetch(`/contact/list/${domainId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка получения контактов: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    this.contactDetails = data;
                    this.showContactPopup = true;
                })
                .catch(error => {
                    this.error = error.message;
                });
        },
        showVisitorDetails(domainId) {
            this.selectedDomain = this.domains.find(domain => domain.id === domainId);
            fetch(`/visit/get-visitors-with-contacts/${domainId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка получения посетителей: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Парсим информацию о контактах
                    data.visitors.forEach(visitor => {
                        visitor.contacts = visitor.contacts.map(contact => {
                            contact.info = JSON.parse(contact.info); // Преобразуем строку JSON в объект
                            return contact;
                        });
                    });
                    this.visitors = data.visitors;
                    this.showVisitorPopup = true;
                })
                .catch(error => {
                    this.error = error.message;
                });
        },
        closePopup() {
            this.showPopup = false;
            this.selectedDomain = null;
        },
        closeContactPopup() {
            this.showContactPopup = false;
            this.contactDetails = [];
        },
        closeVisitorPopup() {
            this.showVisitorPopup = false;
            this.visitors = [];
        }
    },
    template: `
        <div>
            <h2>Список доменов</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID домена</th>
                        <th>Домен</th>
                        <th>Количество посещений</th>
                        <th>Количество посещений за прошлый месяц</th>
                        <th>Количество оставленных контактов</th>
                        <th>Количество посетителей с оставленными контактами</th>
                        <th>Дата последнего оставленного контакта</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="domain in domains" :key="domain.id">
                        <td>{{ domain.id }}</td>
                        <td @click="showDomainDetails(domain)" style="cursor: pointer; color: blue;">{{ domain.domain }}</td>
                        <td>{{ domain.visit_count }}</td>
                        <td>{{ domain.last_month_visits }}</td>
                        <td @click="showContactDetails(domain.id)" style="cursor: pointer; color: blue;">{{ domain.contact_count }}</td>
                        <td @click="showVisitorDetails(domain.id)" style="cursor: pointer; color: blue;">{{ domain.visitor_count }}</td>
                        <td>{{ domain.last_contact_date }}</td>
                    </tr>
                </tbody>
            </table>
            <p v-if="error" style="color: red;">Ошибка: {{ error }}</p>

            <!-- Попап с деталями домена -->
            <div v-if="showPopup" class="popup">
                <div class="popup-content">
                    <span class="close" @click="closePopup">&times;</span>
                    <h2>Детали домена: {{ selectedDomain.domain }}</h2>
                    <p>ID: {{ selectedDomain.id }}</p>
                    <p>Баланс: {{ selectedDomain.balance }}</p>
                    <p>Категории: {{ selectedDomain.categories }}</p>
                </div>
            </div>

            <!-- Попап с контактами -->
            <div v-if="showContactPopup" class="popup">
                <div class="popup-content">
                    <span class="close" @click="closeContactPopup">&times;</span>
                    <h2>Контакты для домена: {{ selectedDomain.domain }}</h2>
                    <ul>
                        <li v-for="contact in contactDetails" :key="contact.id">
                            <strong>Имя:</strong> {{ contact.info.name }}<br>
                            <strong>Телефон:</strong> {{ contact.info.phone }}<br>
                            <strong>Email:</strong> {{ contact.info.email }}<br>
                            <strong>Дата:</strong> {{ contact.created_at }}<br>
                            <hr>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Попап с посетителями -->
            <div v-if="showVisitorPopup" class="popup">
                <div class="popup-content">
                    <span class="close" @click="closeVisitorPopup">&times;</span>
                    <h2>Посетители с оставленными контактами для домена: {{ selectedDomain.domain }}</h2>
                    <ul>
                        <li v-for="visitor in visitors" :key="visitor.id">
                            <strong>ID:</strong> {{ visitor.id }}<br>
                            <strong>Страница:</strong> {{ visitor.page }}<br>
                            <strong>Дата:</strong> {{ visitor.created_at }}<br>
                            <strong>Контакты:</strong>
                            <ul>
                                <li v-for="contact in visitor.contacts" :key="contact.id">
                                    <strong>Имя:</strong> {{ contact.info.name }}<br>
                                    <strong>Телефон:</strong> {{ contact.info.phone }}<br>
                                    <strong>Email:</strong> {{ contact.info.email }}<br>
                                </li>
                            </ul>
                            <hr>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    `
});
