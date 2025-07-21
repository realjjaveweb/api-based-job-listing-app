<template>
  <div>
    <v-card>
      <v-card-title>
        Available Job Positions
        <v-spacer />
        <v-text-field
          v-model="search"
          append-icon="mdi-magnify"
          label="Search jobs..."
          single-line
          hide-details
        />
      </v-card-title>

      <v-card-text>
        <v-row>
          <v-col
            v-for="job in filteredJobs"
            :key="job.id"
            cols="12"
            md="6"
            lg="4"
          >
            <v-card
              class="job-card"
              @click="viewJob(job.id)"
            >
              <v-card-title>{{ job.title }}</v-card-title>
              <v-card-text>
                <v-chip
                  v-if="job.salary"
                  color="success"
                  small
                >
                  <v-icon left>
                    mdi-currency-sign
                  </v-icon>
                  {{ job.salary }}
                </v-chip>
                <p class="mt-2">
                  {{ truncateDescription(job.description) }}
                </p>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  color="primary"
                  text
                  @click.stop="viewJob(job.id)"
                >
                  View Details
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>

        <v-pagination
          v-model="currentPage"
          :length="totalPages"
          class="mt-4"
          @update:model-value="loadJobs"
        />
      </v-card-text>
    </v-card>

    <v-snackbar
      v-model="showError"
      color="error"
    >
      {{ errorMessage }}
      <template #action="{ attrs }">
        <v-btn
          text
          v-bind="attrs"
          @click="showError = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const jobs = ref([]);
const search = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const loading = ref(false);
const showError = ref(false);
const errorMessage = ref("");

const router = useRouter();

const filteredJobs = computed(() => {
  if (!search.value) return jobs.value;

  const searchTerm = search.value.toLowerCase();
  return jobs.value.filter((job) =>
    job.title.toLowerCase().includes(searchTerm),
  );
});

async function loadJobs() {
  loading.value = true;
  try {
    const backendUrl = import.meta.env.VITE_BACKEND_URL;
    const response = await axios.get(
      `${backendUrl}/api/jobs?page=${currentPage.value}&limit=9`,
    );
    if (response.data.success) {
      jobs.value = response.data.data;
      totalPages.value = Math.ceil(response.data.pagination.total / 9);
    } else {
      showError.value = true;
      errorMessage.value = response.data.error || "Failed to load jobs";
    }
  } catch (error) {
    showError.value = true;
    errorMessage.value = "Failed to load jobs. Please try again later.";

    console.error("Error loading jobs:", error);
  } finally {
    loading.value = false;
  }
}

function viewJob(jobId) {
  router.push(`/job/${jobId}`);
}

function truncateDescription(description) {
  return description.length > 150
    ? description.substring(0, 150) + "..."
    : description;
}

onMounted(() => {
  loadJobs();
});
</script>

<style scoped>
.job-card {
  cursor: pointer;
  transition: transform 0.2s;
}

.job-card:hover {
  transform: translateY(-2px);
}
</style>
