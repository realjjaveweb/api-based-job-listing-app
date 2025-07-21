<template>
  <v-card v-if="application">
    <v-card-title>Apply for this position</v-card-title>
    <v-card-text>
      <v-form
        ref="formRef"
        v-model="valid"
      >
        <v-text-field
          v-model="application.firstName"
          label="First Name"
          :rules="[rules.required]"
          required
        />

        <v-text-field
          v-model="application.lastName"
          label="Last Name"
          :rules="[rules.required]"
          required
        />

        <v-text-field
          v-model="application.email"
          label="Email"
          type="email"
          :rules="[rules.required, rules.email]"
          required
        />

        <v-text-field
          v-model="application.phone"
          label="Phone"
          :rules="[rules.required]"
          required
        />

        <v-textarea
          v-model="application.coverLetter"
          label="Cover Letter"
          :rules="[rules.required]"
          rows="4"
          required
        />

        <v-text-field
          v-model="application.resumeUrl"
          label="Resume URL (optional)"
          hint="Link to your resume or portfolio"
        />

        <v-btn
          color="primary"
          block
          :loading="submitting"
          :disabled="!valid"
          @click="submitApplication"
        >
          Submit Application
        </v-btn>
      </v-form>
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
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, defineProps, defineEmits } from "vue";
import axios from "axios";

const rules = {
  required: (value) => {
    if (value === null || value === undefined || value === "") {
      return "Required";
    }
    return true;
  },
  email: (value) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(value) || "Invalid email address";
  },
};

const props = defineProps({
  jobId: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["success", "error"]);

const valid = ref(false);
const submitting = ref(false);
const showMessage = ref(false);
const messageText = ref("");
const messageColor = ref("success");
const formRef = ref(null);

const getEmptyApplicationForm = () => ({
  firstName: "",
  lastName: "",
  email: "",
  phone: "",
  coverLetter: "",
  resumeUrl: "",
});
const application = ref(getEmptyApplicationForm());

const submitApplication = async () => {
  if (!formRef.value.validate()) return;

  submitting.value = true;
  try {
    const backendUrl = import.meta.env.VITE_BACKEND_URL;
    const response = await axios.post(
      `${backendUrl}/api/jobs/${props.jobId}/apply`,
      application.value,
    );
    if (response.data.success) {
      showMessage.value = true;
      messageText.value = "Application submitted successfully!";
      messageColor.value = "success";
      formRef.value.reset();
      application.value = getEmptyApplicationForm();
      emit("success");
    } else {
      showMessage.value = true;
      messageText.value =
        response.data.message || "Failed to submit application";
      messageColor.value = "error";
      emit("error", response.data.message);
    }
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      messageText.value = error.response.data.message;
    } else {
      messageText.value =
        "Failed to submit application. Please try again later.";
      console.error("Error submitting application:", error);
    }
    showMessage.value = true;
    messageColor.value = "error";
    emit("error", messageText.value);
  } finally {
    submitting.value = false;
  }
};
</script>
