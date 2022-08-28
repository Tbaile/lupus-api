#!/usr/bin/env sh

TAG=${IMAGE_TAG:-develop}

echo "Build release images."
docker buildx bake release
echo "Releasing a manifest for the images."
docker manifest create docker.io/tbaile/lupus-app:"$TAG" --amend docker.io/tbaile/lupus-app:"$TAG"-amd64 --amend docker.io/tbaile/lupus-app:"$TAG"-arm64
docker manifest push docker.io/tbaile/lupus-app:"$TAG"
docker manifest create docker.io/tbaile/lupus-web:"$TAG" --amend docker.io/tbaile/lupus-web:"$TAG"-amd64 --amend docker.io/tbaile/lupus-web:"$TAG"-arm64
docker manifest push docker.io/tbaile/lupus-web:"$TAG"
