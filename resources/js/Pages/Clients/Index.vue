<template>
  <div class="container">

    <h1 class="mb-4"></h1>
    <div class="toast" :class="{ 'show': sync }" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header" style="background-color: #55ab55;">
        <strong class="mr-auto" style="color:white">Synchronize Successful</strong>
        <button type="button" class="close" data-dismiss="toast" aria-label="Close" @click="sync = false">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
    <button class="btn btn-sm btn-primary disabled" style="float:right" @click="synchronize()">{{ process ?
      '....syncing' : 'Synchronize' }}</button><br><br>


    <!-- <div class="row mb-3">
      <div class="col-md-6">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Search clients..." v-model="searchQuery">
          <button class="input-group-text" type="button">
            <i class="bi bi-search"></i> </button>
        </div>
      </div>
    </div> -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="d-flex align-items-center">
          <select class="form-select" v-model="filters.status">
            <option value="">All Statuses</option>
            <option value="processing">Processing</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="any">Any</option>


          </select>
          <button class="btn btn-xl btn-primary" style="float:right;margin-left:17px;"
            @click="filter()">Filter</button><br><br>
        </div>
      </div>

    </div>

    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>Order Number</th>
          <th>Status</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(client, k) in clients.data" :key="client.id">
          <td>{{ k + 1 }}</td>
          <td>{{ client.number }}</td>
          <td>{{ client.status }}</td>
          <td>{{ client.total }}</td>

        </tr>
      </tbody>
    </table>
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: !clients.links.prev }">
          <button class="page-link" @click="prevPage">Previous</button>
        </li>
        <!-- <li v-for="n in Math.ceil(clients.total / clients.per_page)" :key="n" class="page-item">
          <button class="page-link" :class="{ active: n === clients.current_page }" @click="goToPage(n)">
            {{ n }}
          </button>
        </li> -->
        <li class="page-item" :class="{ disabled: !clients.links.next }">
          <button class="page-link" @click="nextPage">Next</button>
        </li>
      </ul>
    </nav>

  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import { Inertia, usePage } from '@inertiajs/inertia'; // Import Inertia

export default {
  props: {
    clients: Object,
  },
  data() {
    return {
      searchQuery: '',
      filters: { 'status': '' },
      isEditModalOpen: false,
      selectedClient: null,
      intend: 'create',
      sync: false,
      process: false
      // ... other data properties
    };
  },
  computed: {
    filteredClients() {
      // Implement filtering logic based on searchQuery and filterByStatus
    },
  },
  methods: {
    editClient(client) {
      this.intend = 'Edit';
      this.isEditModalOpen = true;
      this.selectedClient = client;

    },
    createClient() {
      this.intend = 'Create';
      this.isEditModalOpen = true;
      this.selectedClient = {
        name: '',
        email: '',
        id: ''
      };


    },

    handleModalClose() {
      this.isEditModalOpen = false;
      this.selectedClient = null; // Clear selected client after closing modal
    },

    prevPage() {
      if (this.clients.links.prev) {
        Inertia.visit(this.clients.links.prev);
      }
    },
    nextPage() {
      if (this.clients.links.next) {
        Inertia.visit(this.clients.links.next + '&&filters=' + JSON.stringify(this.filters));
      }
    },
    filter() {
      Inertia.visit(route('clients.index', { filters: JSON.stringify(this.filters) }));
    },
    synchronize() {
      this.process = true;
      Inertia.visit(route('clients.index', { synchronize: true }));
    },
    goToPage(page) {
      // Implement logic to fetch data for the specified page using Inertia.visit
      // You can potentially construct the URL based on the pagination links object or
      // use a dedicated action in your Laravel controller to handle pagination requests.
      console.log(`Navigate to page ${page}`); // Placeholder for now
    },
  },
  mounted() {
    let queryString = window.location.search;
    let urlParams = new URLSearchParams(queryString);

    if (urlParams.has('synchronize')) {
      this.sync = true;
      setTimeout(() => {
        urlParams.delete('synchronize');
        history.replaceState({}, '', `${window.location.pathname}?${urlParams.toString()}`);

      }, 2000);

    }

  },
  components: {
    Link
  },
};
</script>
