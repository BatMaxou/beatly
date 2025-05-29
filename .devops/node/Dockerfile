FROM node:22-alpine as main

WORKDIR /app

COPY ./front/package*.json ./

RUN npm install

EXPOSE 5173

FROM main as builder

COPY ./front /app

RUN npm run build

FROM main as dev

COPY ./front /app

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]

FROM nginx:1.25-alpine as prod

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

