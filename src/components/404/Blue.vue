<template>
  <div :class="$style.wrapper">
    <div>
      <div :class="$style.title">
        <span>Страница не найдена</span>
      </div>

      <div :class="$style.home" @click="$router.push('/home')">
        <span>начать с начала</span>
      </div>
    </div>
  </div>
</template>

<style module>
.wrapper {
  background-color: #173e3ba8;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #ffffff;
  width: 100%;
  height: 100vh;
  text-transform: uppercase;
  font-size: 20px;
  text-align: center;
}
.title {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding: 1rem 0;
  border-bottom: 1px solid white;
}
.home {
  cursor: pointer;
  margin-top: 1rem;
  font-size: 14px;
  background-color: #1d676296;
  padding: 0.7rem;
  transition: 0.3s background-color ease-out;
}
.home:hover {
  background-color: #072d2b96;
}
</style>
