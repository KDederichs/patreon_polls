import { PatreonCampaignTier } from '@/types/entity/PatreonCampaignTier'
import { Card, CardBody, CardHeader, Checkbox, Spacer } from '@nextui-org/react'
import { Input } from '@nextui-org/input'

interface Props {
  selectedTiers: PatreonCampaignTier[]
}

export default function TierVoteConfig(
  {
    selectedTiers = []
  } : Props
) {

  return (
    <>
      {selectedTiers.map((selectedTier) => (
        <div key={selectedTier.id}>
          <Spacer y={4} />
          <Card>
            <CardHeader>
              <h3 className="text-medium">
                {selectedTier.tierName}
              </h3>
            </CardHeader>
            <CardBody>
              <div className="grid gap-5 grid-cols-2">
                <Checkbox>
                  Can add options
                </Checkbox>
                <Input type={'number'} label="How many?" />
              </div>
              <div className="grid gap-5 grid-cols-2 mt-2">
                <Checkbox>
                  Limited votes
                </Checkbox>
                <Input type={'number'} label="How many?" />
              </div>
              <Input type={'number'} label="Voting power" className="mt-2" />
            </CardBody>
          </Card>
        </div>
      ))}
    </>
  )

}
