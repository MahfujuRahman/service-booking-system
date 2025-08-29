<template>
  <div class="container-fluid">
    <!--Start Dashboard Content-->
    <div class="card mt-3">
      <div class="card-content">
        <div class="row row-group m-0">
          <div class="col-12 col-lg-6 col-xl-3 border-light">
            <div class="card-body">
              <h5 class="text-white mb-0">
                {{ data.total_income }}
                <!-- <span class="float-right"><i class="fa fa-users"></i></span> -->
              </h5>
              <div class="progress my-3" style="height: 3px">
                <!-- <div class="progress-bar" style="width: 55%"></div> -->
              </div>
              <p class="mb-0 text-white small-font">
                Total Income
                <span class="float-right">
                  <!-- <i class="zmdi zmdi-long-arrow-up"></i> -->
                </span>
              </p>
            </div>
          </div>
          <div class="col-12 col-lg-6 col-xl-3 border-light">
            <div class="card-body">
              <h5 class="text-white mb-0">
                {{ data.total_services }}
                <!-- <span class="float-right"><i class="fa fa-book"></i></span> -->
              </h5>
              <div class="progress my-3" style="height: 3px">
                <!-- <div class="progress-bar" style="width: 55%"></div> -->
              </div>
              <p class="mb-0 text-white small-font">
                Total Services
                <span class="float-right">
                  <!-- <i class="zmdi zmdi-long-arrow-up"></i> -->
                </span>
              </p>
            </div>
          </div>
          <div class="col-12 col-lg-6 col-xl-3 border-light">
            <div class="card-body">
              <h5 class="text-white mb-0">
                {{ data.total_bookings }}
                <!-- <span class="float-right"><i class="fa fa-eye"></i></span> -->
              </h5>
              <div class="progress my-3" style="height: 3px">
                <!-- <div class="progress-bar" style="width: 55%"></div> -->
              </div>
              <p class="mb-0 text-white small-font">
                Total Bookings
                <span class="float-right">
                  <!-- <i class="zmdi zmdi-long-arrow-up"></i> -->
                </span>
              </p>
            </div>
          </div>
          <div class="col-12 col-lg-6 col-xl-3 border-light">
            <div class="card-body">
              <h5 class="text-white mb-0">
                {{ data.total_users }}
                <!-- <span class="float-right"><i class="fa fa-envira"></i></span> -->
              </h5>
              <div class="progress my-3" style="height: 3px">
                <!-- <div class="progress-bar" style="width: 55%"></div> -->
              </div>
              <p class="mb-0 text-white small-font">
                Total Users
                <span class="float-right">
                  <!-- <i class="zmdi zmdi-long-arrow-up"></i> -->
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!--End Row-->
    <div class="row">
      <div class="col-12 col-lg-12">
        <div class="card">
          <div class="card-header">Latest Purchase Orders</div>
          <table class="table table-hover text-center table-bordered table_responsive">
            <thead>
              <tr>
                <th>#</th>
                <th>Order Id</th>
                <th>User Id</th>
                <th>Service Id</th>
                <th>Status</th>
                <th>Date</th>

              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in data?.latest_bookings" :key="index">
                <td>{{ index + 1 }}</td>
                <td>{{ item.id ?? "N/A" }}</td>
                <td>{{ item.user.first_name ?? "N/A" }} {{ item.user.last_name ?? "N/A" }}</td>
                <td>{{ item.service.name ?? "N/A" }}</td>
                <td>{{ item.status ?? "N/A" }}</td>
                <td>{{ item.booking_date ?? "N/A" }}</td>

              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!--End Dashboard Content-->
    <!--start overlay-->
    <div class="overlay"></div>
    <!--end overlay-->
  </div>
  <!-- End container-fluid-->
</template>

<script>
export default {
  data: () => ({
    data: {},
  }),
  created: async function () {
    await this.get_all_dashboard_data();
  },
  methods: {
    get_all_dashboard_data: async function () {
      let response = await axios.get("get-all-dashboard-data");
      if (response.status == 200) {
        this.data = response.data.data;
      }
    },
  },
};
</script>

<style></style>
