<template>
  <div>
    <v-btn
      class="mb-4"
      @click="$router.push('/')"
    >
      <v-icon left>
        mdi-arrow-left
      </v-icon>
      Back to Jobs
    </v-btn>

    <v-row v-if="job">
      <v-col
        cols="12"
        md="8"
      >
        <v-card>
          <v-card-title>{{ job.title }}</v-card-title>
          <v-card-text>
            <v-row>
              <v-col
                v-if="job.salary"
                cols="12"
                sm="6"
              >
                <v-chip
                  color="success"
                  class="mb-2"
                >
                  <v-icon left>
                    mdi-currency-sign
                  </v-icon>
                  {{ job.salary }}
                </v-chip>
              </v-col>
            </v-row>

            <v-divider class="my-4" />

            <h3>Job Description</h3>
            <p class="mt-2">
              {{ job.description }}
            </p>

            <v-row class="mt-4">
              <v-col
                cols="12"
                sm="6"
              >
                <strong>Posted:</strong> {{ formatDate(job.createdAt) }}
              </v-col>
              <v-col
                cols="12"
                sm="6"
              >
                <strong>Updated:</strong> {{ formatDate(job.updatedAt) }}
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col
        cols="12"
        md="4"
      >
        <JobApplicationForm :job-id="job.id" />
      </v-col>
    </v-row>

    <v-snackbar
      v-model="showMessage"
      :color="messageColor"
    >
      {{ messageText }}
      <template #action="{ attrs }">
        <v-btn
          text
          v-bind="attrs"
          @click="showMessage = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import JobApplicationForm from "./JobApplicationForm.vue";

const route = useRoute();
const job = ref(null);
const valid = ref(false);
const submitting = ref(false);
const showMessage = ref(false);
const messageText = ref("");
const messageColor = ref("success");
const formRef = ref(null);

const loadJob = async () => {
  try {
    const backendUrl = import.meta.env.VITE_BACKEND_URL;
    const response = await axios.get(
      `${backendUrl}/api/jobs/${route.params.id}`,
    );
    if (response.data.success) {
      job.value = response.data.data;
    } else {
      showMessage.value = true;
      messageText.value = response.data.error || "Failed to load job details";
      messageColor.value = "error";
    }
  } catch (error) {
    showMessage.value = true;
    messageText.value = "Failed to load job details. Please try again later.";
    messageColor.value = "error";
    console.error("Error loading job:", error);
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString();
};

onMounted(() => {
  loadJob();
});
</script>
