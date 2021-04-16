<template>
  <div :class="$style.wrapper">
    <div :class="$style.video" v-if="isScenarioDone">
      <video
        width="100%"
        height="auto"
        preload="auto"
        autoplay="autoplay"
        loop="loop"
      >
        <source src="../../assets/rain.mp4" type="video/mp4" />
        <source src="../../assets/rain.webm" type="video/webm" />
      </video>
    </div>

    <el-collapse-transition>
      <div :class="$style.overlay" v-if="!isScenarioDone">
        <div :class="$style.heading" v-if="!audioInstance">
          <div :class="$style.title">
            <span>Этой страницы не существует . . .</span>
            <span>This page is not exists . . .</span>
            <span>Its exists only in your mind . . .</span>
          </div>

          <div :class="$style.title">
            <span
              >У тебя могла возникнуть мысль, что ты оказался здесь
              случайно</span
            >
            <p>
              <span
                >Задумайся, быть может, это не так ? Где ты есть сейчас на самом
                деле ?</span
              >
            </p>
            <p style="text-transform:uppercase;">
              Если бы ты не мог проснуться, как бы ты узнал, что сон, а что
              действительность?
            </p>
          </div>
        </div>

        <div v-else :class="$style.heading">
          <span>Прислушайся . . .</span>
        </div>

        <div
          :class="$style.choice"
          v-if="!isScenarioDone"
          @click="startIntroDialog"
        >
          <span>сделать выбор</span>
        </div>

        <transition name="el-fade-in">
          <div v-if="isWannaLeaveThisPlace" :class="$style.dialog">
            <div
              @mouseenter="startChoiceAudioDialog('dialog_leave.mp3')"
              @click="leaveThisPlace"
            >
              проснуться
            </div>
            <el-divider direction="vertical" />
            <div
              @mouseenter="startChoiceAudioDialog('dialog_stay.mp3', true)"
              @click="stayInThisPlace"
            >
              остаться
            </div>
          </div>
        </transition>

        <div :class="$style.stats">
          <div>
            посещена: {{ choice.visited }}
            {{ getDeclination(choice.visited) }}
          </div>

          <div>
            проснулись: {{ choice.leaved }}
            {{ getDeclination(choice.leaved) }}
          </div>

          <div>
            оставались: {{ choice.stayed }}
            {{ getDeclination(choice.stayed) }}
          </div>
        </div>
      </div>
    </el-collapse-transition>
  </div>
</template>

<script>
import { getRandomInt, declOfNum } from "@/utils/common";

export default {
  data() {
    return {
      isWannaLeaveThisPlace: false,
      isScenarioDone: false,
      hasIntroDialogEnded: false,
      isStayingDialogDone: false,
      isLEavingDialogDone: false,
      isDialogPlaying: false,
      audioInstance: null,

      choice: {
        visited: 0,
        stayed: 0,
        leaved: 0
      },

      audioList: {
        0: "183.mp3",
        1: "36-dreamscape.mp3",
        2: "36-inside.mp3",
        3: "alio-die-robert-rich-mycelia.mp3"
      }
    };
  },

  async created() {
    this.$router.beforeEach((to, from, next) => {
      if (this.audioInstance !== null) this.audioInstance.pause();
      this.audioInstance = null;
      return next();
    });

    this.choice = {
      ...(
        await this.$HTTPGet({
          route: "/common/get-choice"
        })
      )[0]
    };
  },

  methods: {
    startIntroDialog() {
      if (this.isDialogPlaying || this.hasIntroDialogEnded) return;

      const audio = new Audio(require("../../assets/audio/dialog_intro.mp3"));
      audio.play();
      this.isDialogPlaying = true;

      audio.addEventListener("ended", () => {
        this.isDialogPlaying = false;
        this.hasIntroDialogEnded = true;
        this.isWannaLeaveThisPlace = !this.isWannaLeaveThisPlace;
      });
    },

    startChoiceAudioDialog(src, isStaying = false) {
      if (this.isDialogPlaying) return;
      if (isStaying && this.isStayingDialogDone) return;
      if (!isStaying && this.isLEavingDialogDone) return;

      const audio = new Audio(require(`../../assets/audio/${src}`));
      audio.play();
      this.isDialogPlaying = true;

      audio.addEventListener("ended", () => {
        this.isDialogPlaying = false;
        isStaying
          ? (this.isStayingDialogDone = true)
          : (this.isLEavingDialogDone = true);
      });
    },

    stayInThisPlace() {
      if (this.isDialogPlaying) return;
      if (this.audioInstance) return this.$onError("Выбор уже сделан");
      this.isWannaLeaveThisPlace = false;
      this.$onSuccess("Выбор сделан", 3666);
      this.choice.stayed++;

      setTimeout(() => this.startPlayRelaxMusic(), 1500);
      setTimeout(() => (this.isScenarioDone = true), 3666);

      this.$HTTPGet({
        route: "/common/set-choice",
        payload: { stayed: true }
      });
    },

    leaveThisPlace() {
      if (this.isDialogPlaying) return;
      if (this.audioInstance) return this.$onError("Выбор уже сделан");

      this.$onSuccess("С возвращением");
      this.$router.push("/");
      this.$HTTPGet({
        route: "/common/set-choice",
        payload: {
          leaved: true
        }
      });
    },

    startPlayRelaxMusic() {
      let randomAudio = this.audioList[getRandomInt(0, 3)];
      const audio = new Audio(require(`../../assets/audio/${randomAudio}`));
      this.audioInstance = audio;

      audio.addEventListener("canplaythrough", () => audio.play());
      audio.addEventListener("ended", () => {
        randomAudio = this.audioList[getRandomInt(0, 3)];
        audio.src = require(`../../assets/audio/${randomAudio}`);
        audio.play();
      });
    },

    getDeclination(count) {
      return declOfNum(count, ["раз", "раза", "раз"]);
    }
  }
};
</script>

<style module>
body {
  overflow: hidden;
}
.wrapper {
  background-image: url("../../assets/bg_secondary.png");
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  height: 100vh;
  display: flex;
}
.wrapper p {
  margin: 0.5rem 0;
}
.overlay {
  font-family: "Noto Sans", sans-serif;
  background-color: #2f676d85;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  width: 100%;
  padding: 1.5rem;
  text-transform: uppercase;
  font-size: 20px;
  height: 100%;
  align-self: center;
  text-align: center;
}
.heading {
  min-height: 200px;
}
.heading .title:first-child {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.heading .title:first-child,
.heading .title:first-child span {
  padding: 1rem 0;
}
.heading .title:first-child span:first-child {
  border-bottom: 1px solid white;
}
.heading .title:last-child {
  text-transform: none;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  font-size: 16px;
}
.choice {
  cursor: pointer;
  position: relative;
  top: 5%;
  width: 20%;
  font-size: 14px;
  background-color: #1d676296;
  padding: 0.7rem;
  transition: 0.3s background-color ease-out;
}
.choice:hover,
.stats:hover {
  background-color: #072d2b96;
}
.dialog {
  position: absolute;
  top: 70%;
  display: flex;
  font-size: 17px;
  align-items: flex-start;
}
.dialog div {
  cursor: pointer;
  transition: all 0.3s linear;
}
.dialog div:hover {
  text-decoration: underline;
}
.dialog div:hover:first-child {
  color: #234d71;
  text-shadow: 1px 3px 7px #234d71;
}
.dialog div:hover:last-child {
  color: #ca3a3a;
  text-shadow: 1px 3px 7px #ff2424;
}
.stats {
  position: relative;
  right: 40%;
  top: 25%;
  font-size: 14px;
  background-color: #1d676296;
  transition: 0.3s background-color ease-out;
  padding: 1rem 2.5rem;
}
.stats div {
  margin-bottom: 0.2rem;
}
.video {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  overflow: hidden;
  z-index: 1;
  background-size: cover;
  opacity: 0.3;
}
.video > video {
  position: absolute;
  top: 0;
  left: 0;
  min-width: 100%;
  min-height: 100%;
  width: auto;
  height: auto;
}
@supports (object-fit: cover) {
  .video > video {
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}
</style>
