#!/usr/bin/env sh

TAG=${IMAGE_TAG:-develop}

echo "Build release images."
docker buildx bake release
echo "Releasing a manifest for the images."
docker manifest create tbaile/lupus-app:"$TAG" --amend tbaile/lupus-app:"$TAG"-amd64 --amend tbaile/lupus-app:"$TAG"-arm64
docker manifest push tbaile/lupus-app:"$TAG"
docker manifest create tbaile/lupus-web:"$TAG" --amend tbaile/lupus-web:"$TAG"-amd64 --amend tbaile/lupus-web:"$TAG"-arm64
docker manifest push tbaile/lupus-web:"$TAG"
